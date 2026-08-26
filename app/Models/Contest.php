<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contest extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'name',
        'description',
        'target_type_id',
        'target',
        'target_value',
        'participation_type',
        'participant_mode',
        'achievement_source',
        'starts_at',
        'ends_at',
        'reward',
        'reward_type',
        'reward_second',
        'reward_third',
        'min_achievement',
        'counting_method',
        'tie_breaker',
        'eligibility',
        'is_active',
        'sort_order',
        'payout_processed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'is_active' => 'boolean',
        'target_value' => 'decimal:2',
        'min_achievement' => 'decimal:2',
        'payout_processed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // This app's tables are MyISAM (no FK support), so ON DELETE CASCADE in the
        // migrations is not actually enforced by the database — cascade manually.
        static::deleting(function (self $contest) {
            $contest->registrations()->delete();
            $contest->achievements()->delete();
            $contest->pointRules()->delete();
            $contest->incentiveEntries()->update(['contest_id' => null]);
        });
    }

    public function pointRules(): HasMany
    {
        return $this->hasMany(ContestPointRule::class);
    }

    public function targetType(): BelongsTo
    {
        return $this->belongsTo(ContestTargetType::class);
    }

    public function pointRuleFor(string $leadStatus): ?ContestPointRule
    {
        return $this->pointRules->firstWhere('lead_status', $leadStatus);
    }

    public function isCrmDriven(): bool
    {
        return $this->achievement_source === 'crm';
    }

    public function unitLabel(): string
    {
        return $this->isCrmDriven() ? 'pts' : '₹';
    }

    public function formatAmount(float $amount): string
    {
        return $this->isCrmDriven()
            ? number_format($amount, 0) . ' pts'
            : '₹' . number_format($amount, 0);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(ContestParticipant::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'contest_participants')->withTimestamps();
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(ContestAchievement::class);
    }

    public function incentiveEntries(): HasMany
    {
        return $this->hasMany(IncentiveEntry::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('ends_at', '>=', now()->toDateString())->orderBy('starts_at');
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where('ends_at', '<', now()->toDateString())->orderByDesc('starts_at');
    }

    public function isRegisteredBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->registrations->contains('user_id', $user->id);
    }

    public function hasEnded(): bool
    {
        return $this->ends_at->endOfDay()->isPast();
    }

    /**
     * Draft / Active / Completed — computed, not stored. "Draft" means the
     * admin hasn't published it (is_active = false). Once published, it's
     * "Active" until its end date passes, then "Completed" automatically —
     * no cron needed since nothing counts toward a contest after end date
     * anyway (see finalizeIfEnded()).
     */
    public function displayStatus(): string
    {
        if (! $this->is_active) {
            return 'draft';
        }

        return $this->hasEnded() ? 'completed' : 'active';
    }

    public function daysRemaining(): int
    {
        if ($this->hasEnded()) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays($this->ends_at->copy()->startOfDay()) + 1;
    }

    public function totalAchievementFor(User $user): float
    {
        return (float) $this->achievements()->where('user_id', $user->id)->sum('amount');
    }

    public function progressPercentFor(User $user): int
    {
        $target = (float) $this->target_value;

        if ($target <= 0) {
            return 0;
        }

        return (int) round($this->totalAchievementFor($user) / $target * 100);
    }

    public function remainingFor(User $user): float
    {
        return max(0, (float) $this->target_value - $this->totalAchievementFor($user));
    }

    /**
     * Every participant ranked by total achievement (desc), tie-broken by
     * whoever joined the contest first. Used by both the Contest Tracker
     * and the Leaderboard.
     */
    public function rankedParticipants(): \Illuminate\Support\Collection
    {
        $achievedByUser = $this->achievements()
            ->selectRaw('user_id, SUM(amount) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        return $this->participants()
            ->get()
            ->map(function (User $participant) use ($achievedByUser) {
                $achieved = (float) ($achievedByUser[$participant->id] ?? 0);
                $participant->achieved_amount = $achieved;
                $participant->progress_percent = $this->target_value > 0
                    ? (int) round($achieved / (float) $this->target_value * 100)
                    : 0;

                return $participant;
            })
            ->sortByDesc(fn (User $participant) => $participant->achieved_amount)
            ->values()
            ->map(function (User $participant, int $index) {
                $participant->rank = $index + 1;

                return $participant;
            });
    }

    /**
     * Top achievers eligible to win — excludes anyone under the minimum
     * achievement rule, if one is set.
     */
    public function winners(int $count = 3): \Illuminate\Support\Collection
    {
        $minAchievement = (float) ($this->min_achievement ?? 0);

        return $this->rankedParticipants()
            ->filter(fn (User $participant) => $participant->achieved_amount >= $minAchievement && $participant->achieved_amount > 0)
            ->take($count)
            ->values();
    }

    /**
     * Once an active contest's end date has passed, generate the winners'
     * Incentives & Earnings entries exactly once. Safe to call from any page
     * that loads a contest — idempotent via payout_processed_at.
     */
    public function finalizeIfEnded(): void
    {
        if (! $this->is_active || ! $this->hasEnded() || $this->payout_processed_at) {
            return;
        }

        $winners = $this->winners(3);
        $rewardTiers = [$this->reward, $this->reward_second, $this->reward_third];

        foreach ($winners as $index => $winner) {
            $rewardText = $rewardTiers[$index] ?? null;

            if (! $rewardText) {
                continue;
            }

            $amount = (float) preg_replace('/[^0-9.]/', '', $rewardText) ?: 0;
            $place = ['Winner', 'Runner-up', 'Second Runner-up'][$index] ?? ($index + 1) . 'th Place';

            IncentiveEntry::create([
                'user_id' => $winner->id,
                'title' => "{$this->name} — Contest {$place}",
                'amount' => $amount,
                'type' => $this->reward_type ?: 'bonus',
                'source' => 'contest',
                'contest_id' => $this->id,
                'awarded_at' => $this->ends_at,
                'note' => $rewardText,
            ]);
        }

        $this->update(['payout_processed_at' => now()]);
    }

    public function targetLabel(): string
    {
        return $this->target ?: number_format((float) $this->target_value, 0);
    }
}
