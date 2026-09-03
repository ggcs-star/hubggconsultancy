<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'designation',
        'email',
        'password',
        'role',
        'profile_completed',
        'salesperson_status',
        'status',
        'phone',
        'gg_user_id',
        'address',
        'city',
        'highest_qualification',
        'institution_name',
        'field_of_study',
        'education_year',
        'pincode',
        'state',
        'country',
        'referral_code',
        'referred_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'profile_completed' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $user) {
            if (empty($user->referral_code)) {
                $user->referral_code = static::generateUniqueReferralCode();
            }
        });

        // Every user gets the "Other" support product so they can always
        // raise a ticket even when nothing else fits — see
        // SaasProductSupportIssueSeeder for the one-time backfill of
        // existing users when this product didn't exist yet.
        static::created(function (self $user) {
            $otherProduct = SaasProduct::where('slug', 'other')->first();

            if ($otherProduct) {
                $otherProduct->users()->syncWithoutDetaching([$user->id]);
            }
        });
    }

    protected static function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Test/QA accounts listed in TEAM_API_BYPASS_EMAILS skip GG Prime
     * verification at register/login, and are the only accounts allowed to
     * manually edit gg_user_id/phone on their profile (since they never get
     * that data auto-synced from a real GG Prime membership).
     */
    public static function isGgBypassEmail(?string $email): bool
    {
        if (! $email) {
            return false;
        }

        $bypassList = array_map('strtolower', config('services.team_api.bypass_emails', []));

        return in_array(strtolower($email), $bypassList, true);
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(SaasProduct::class, 'saas_product_user')->withTimestamps();
    }

    public function onboardingAssessmentAnswers(): HasMany
    {
        return $this->hasMany(OnboardingAssessmentAnswer::class);
    }

    public function assignedCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_user')->withTimestamps();
    }

    public function registeredEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_registrations')->withTimestamps();
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'referred_by');
    }

    /**
     * Everyone who signed up using this user's referral link, regardless of
     * their current salesperson status.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(self::class, 'referred_by');
    }

    /**
     * The subset of referrals that actually count as "My Team" — a referred
     * signup only joins the team once they're an approved salesperson.
     */
    public function teamMembers(): HasMany
    {
        return $this->referrals()->where('salesperson_status', 'approved');
    }

    /**
     * Earnings this salesperson has received for their team's activity —
     * logged manually by an admin against a specific team member.
     */
    public function referralEarnings(): HasMany
    {
        return $this->hasMany(ReferralEarning::class, 'referrer_id');
    }

    public function totalReferralEarnings(): float
    {
        return (float) $this->referralEarnings()->sum('amount');
    }

    /**
     * Contest rewards and admin-granted incentives — the "Incentives & Earnings" ledger.
     */
    public function incentiveEntries(): HasMany
    {
        return $this->hasMany(IncentiveEntry::class);
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function onboardingChecklistCompletions(): HasMany
    {
        return $this->hasMany(OnboardingChecklistCompletion::class);
    }

    /**
     * Points earned across every LMS course quiz assigned to this user —
     * both mid-lesson (YouTube video) checkpoints and standalone module
     * quizzes, since both are stored as CourseQuizCheckpoint rows scoped
     * to course_id. Deliberately excludes the separate Onboarding
     * Assessment, which has its own scoring (OnboardingAssessmentScorer).
     */
    public function lmsPoints(): object
    {
        $courseIds = $this->assignedCourses()->pluck('courses.id');

        $checkpointIds = CourseQuizCheckpoint::whereIn('course_id', $courseIds)->pluck('id');

        $totalPoints = (int) CourseQuizQuestion::whereIn('quiz_checkpoint_id', $checkpointIds)->sum('points');

        $earnedPoints = (int) QuizAnswer::where('user_id', $this->id)
            ->whereIn('quiz_checkpoint_id', $checkpointIds)
            ->whereNotNull('points_awarded')
            ->sum('points_awarded');

        return (object) [
            'earned' => $earnedPoints,
            'total' => $totalPoints,
            'percent' => $totalPoints > 0 ? (int) round($earnedPoints / $totalPoints * 100) : null,
        ];
    }

    /**
     * Points earned across every published Resource's video-checkpoint quizzes.
     * Resources have no per-user assignment (unlike Courses) — every published
     * resource counts toward every user's total.
     */
    public function resourcePoints(): object
    {
        $checkpointIds = ResourceCheckpoint::whereIn(
            'resource_id',
            Resource::published()->pluck('id')
        )->pluck('id');

        $totalPoints = (int) ResourceQuizQuestion::whereIn('resource_checkpoint_id', $checkpointIds)->sum('points');

        $earnedPoints = (int) ResourceQuizAnswer::where('user_id', $this->id)
            ->whereIn('resource_checkpoint_id', $checkpointIds)
            ->whereNotNull('points_awarded')
            ->sum('points_awarded');

        return (object) [
            'earned' => $earnedPoints,
            'total' => $totalPoints,
            'percent' => $totalPoints > 0 ? (int) round($earnedPoints / $totalPoints * 100) : null,
        ];
    }

    /**
     * LMS course points + Resource points combined into the one "pts" figure
     * shown in the topbar.
     */
    public function combinedPoints(): object
    {
        $lms = $this->lmsPoints();
        $resources = $this->resourcePoints();

        $earned = $lms->earned + $resources->earned;
        $total = $lms->total + $resources->total;

        return (object) [
            'earned' => $earned,
            'total' => $total,
            'percent' => $total > 0 ? (int) round($earned / $total * 100) : null,
        ];
    }

    /**
     * Points earned from CRM-driven contests only — manual/revenue contests
     * award ₹ amounts, not points, so they're deliberately excluded here to
     * avoid mixing units in the gamification total below.
     */
    public function totalContestPoints(): int
    {
        return (int) ContestAchievement::where('user_id', $this->id)
            ->whereHas('contest', fn ($query) => $query->where('achievement_source', 'crm'))
            ->sum('amount');
    }

    /**
     * The single "points" figure used for the dashboard rank/leaderboard —
     * LMS + resource quiz points plus CRM-contest points.
     */
    public function totalPoints(): int
    {
        return $this->combinedPoints()->earned + $this->totalContestPoints();
    }

    /**
     * Points earned within a window — same sources as totalPoints() but
     * scoped by when the quiz was graded / contest points were logged, so
     * the Hall of Fame can rank "this month" instead of only all-time.
     */
    public function pointsEarnedBetween(?\Illuminate\Support\Carbon $from, ?\Illuminate\Support\Carbon $to): int
    {
        $lmsPoints = (int) QuizAnswer::where('user_id', $this->id)
            ->whereNotNull('points_awarded')
            ->when($from, fn ($query) => $query->where('graded_at', '>=', $from))
            ->when($to, fn ($query) => $query->where('graded_at', '<=', $to))
            ->sum('points_awarded');

        $resourcePoints = (int) ResourceQuizAnswer::where('user_id', $this->id)
            ->whereNotNull('points_awarded')
            ->when($from, fn ($query) => $query->where('graded_at', '>=', $from))
            ->when($to, fn ($query) => $query->where('graded_at', '<=', $to))
            ->sum('points_awarded');

        $contestPoints = (int) ContestAchievement::where('user_id', $this->id)
            ->whereHas('contest', fn ($query) => $query->where('achievement_source', 'crm'))
            ->when($from, fn ($query) => $query->where('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->where('created_at', '<=', $to))
            ->sum('amount');

        return $lmsPoints + $resourcePoints + $contestPoints;
    }

    public function certificatesCountBetween(?\Illuminate\Support\Carbon $from = null, ?\Illuminate\Support\Carbon $to = null): int
    {
        return $this->certificates()
            ->when($from, fn ($query) => $query->where('issued_at', '>=', $from))
            ->when($to, fn ($query) => $query->where('issued_at', '<=', $to))
            ->count();
    }

    /**
     * Average quiz score across every course this user holds a certificate
     * for — the "learning score" metric on Achiever / Hall of Fame cards.
     */
    public function averageCourseScorePercent(): ?int
    {
        $percents = $this->certificates()
            ->with('course')
            ->get()
            ->map(fn (Certificate $certificate) => $certificate->course?->scoreFor($this)->percent)
            ->filter(fn ($percent) => $percent !== null);

        return $percents->isEmpty() ? null : (int) round($percents->avg());
    }

    public function leadsWonCountBetween(?\Illuminate\Support\Carbon $from = null, ?\Illuminate\Support\Carbon $to = null): int
    {
        return Lead::where('assigned_to', $this->id)
            ->where('status', 'won')
            ->when($from, fn ($query) => $query->where('won_at', '>=', $from))
            ->when($to, fn ($query) => $query->where('won_at', '<=', $to))
            ->count();
    }

    /**
     * Distinct calendar days with a lesson completed — used as the "Most
     * Consistent Learner" signal instead of a raw lesson count, since a raw
     * count would favor a single binge session over steady daily habits.
     */
    public function activeLearningDaysBetween(?\Illuminate\Support\Carbon $from = null, ?\Illuminate\Support\Carbon $to = null): int
    {
        return (int) CourseLessonProgress::where('user_id', $this->id)
            ->where('completed', true)
            ->when($from, fn ($query) => $query->where('completed_at', '>=', $from))
            ->when($to, fn ($query) => $query->where('completed_at', '<=', $to))
            ->selectRaw('COUNT(DISTINCT DATE(completed_at)) as days')
            ->value('days');
    }

    public static function tierFor(int $points): string
    {
        return match (true) {
            $points >= 7000 => 'Platinum Performer',
            $points >= 3000 => 'Gold Performer',
            $points >= 1000 => 'Silver Performer',
            default => 'Bronze Performer',
        };
    }

    public function tier(): string
    {
        return static::tierFor($this->totalPoints());
    }
}
