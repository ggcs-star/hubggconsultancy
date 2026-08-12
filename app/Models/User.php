<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
        'email',
        'password',
        'role',
        'profile_completed',
        'salesperson_status',
        'status',
        'phone',
        'address',
        'city',
        'highest_qualification',
        'institution_name',
        'field_of_study',
        'education_year',
        'pincode',
        'state',
        'country',
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

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
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
}
