<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'source',
        'campaign_id',
        'product',
        'expected_value',
        'priority',
        'status',
        'won_at',
        'assigned_to',
        'next_follow_up_at',
        'created_by',
    ];

    protected $casts = [
        'next_follow_up_at' => 'date',
        'expected_value' => 'decimal:2',
        'won_at' => 'datetime',
    ];

    /** Statuses that no longer count as "in play" — the deal is closed either way. */
    public const TERMINAL_STATUSES = ['won', 'lost', 'not_interested', 'invalid'];

    /** The primary forward pipeline, in order — used for the funnel widget. */
    public const PIPELINE_STATUSES = ['new', 'contacted', 'interested', 'qualified', 'proposal', 'negotiation', 'won'];

    protected static function booted(): void
    {
        // This app's tables are MyISAM (no FK support), so ON DELETE CASCADE in the
        // migrations is not actually enforced by the database — cascade manually.
        static::deleting(function (self $lead) {
            $lead->notes()->delete();
        });

        // Track exactly when a lead became "won" — separate from updated_at,
        // which changes on any edit (a note, a reassignment) and would make
        // "won this month" reporting wrong.
        static::saving(function (self $lead) {
            if ($lead->isDirty('status')) {
                $lead->won_at = $lead->status === 'won' ? now() : null;
            }
        });

        // A lead moving into a stage that a CRM-driven contest has a point rule
        // for automatically earns the assignee that many points — see
        // awardContestPointsForCurrentStatus() for the idempotency guard.
        static::updated(function (self $lead) {
            if ($lead->wasChanged('status')) {
                $lead->awardContestPointsForCurrentStatus();
            }
        });
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class)->latest();
    }

    public function contestAchievements(): HasMany
    {
        return $this->hasMany(ContestAchievement::class);
    }

    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeNeedsFollowUp(Builder $query): Builder
    {
        return $query->whereNotIn('status', self::TERMINAL_STATUSES)
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<=', now()->toDateString());
    }

    public function isOverdue(): bool
    {
        return $this->next_follow_up_at
            && ! in_array($this->status, self::TERMINAL_STATUSES, true)
            && $this->next_follow_up_at->isPast();
    }

    public static function statusLabels(): array
    {
        return [
            'new' => 'New',
            'contacted' => 'Contacted',
            'interested' => 'Interested',
            'qualified' => 'Qualified',
            'proposal' => 'Proposal / Demo',
            'negotiation' => 'Negotiation',
            'won' => 'Won',
            'lost' => 'Lost',
            'not_interested' => 'Not Interested',
            'invalid' => 'Invalid',
            'follow_up_later' => 'Follow-up Later',
        ];
    }

    public function statusLabel(): string
    {
        return static::statusLabels()[$this->status] ?? ucfirst($this->status);
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'won' => 'badge-green',
            'lost', 'not_interested', 'invalid' => 'bg-red-50 text-red-600',
            'interested', 'qualified', 'proposal', 'negotiation', 'follow_up_later' => 'badge-amber',
            default => 'badge-slate',
        };
    }

    public function priorityBadgeClass(): string
    {
        return match ($this->priority) {
            'high' => 'bg-red-50 text-red-600',
            'medium' => 'badge-amber',
            default => 'badge-slate',
        };
    }

    /**
     * Other leads sharing this phone number — the basic "is this a fake or
     * duplicate lead" signal, surfaced as a warning rather than a hard block
     * (a returning genuine customer can legitimately share a number).
     */
    public static function duplicatesFor(string $phone, ?int $excludeId = null): \Illuminate\Support\Collection
    {
        $phone = trim($phone);

        if ($phone === '') {
            return collect();
        }

        return static::where('phone', $phone)
            ->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId))
            ->get();
    }

    /**
     * Awards contest points when this lead's new status matches a point rule
     * on a CRM-driven contest the assignee is participating in. Guarded by a
     * firstOrCreate keyed on (contest, user, lead, status) so re-saving the
     * same status, or it flipping back and forth, never double-counts.
     */
    public function awardContestPointsForCurrentStatus(): void
    {
        if (! $this->assigned_to) {
            return;
        }

        $rules = ContestPointRule::where('lead_status', $this->status)
            ->whereHas('contest', fn ($query) => $query->where('is_active', true)->where('achievement_source', 'crm'))
            ->with('contest')
            ->get();

        foreach ($rules as $rule) {
            $contest = $rule->contest;

            if ($contest->hasEnded()) {
                continue;
            }

            $isParticipant = $contest->participants()->where('users.id', $this->assigned_to)->exists();

            if (! $isParticipant) {
                continue;
            }

            ContestAchievement::firstOrCreate(
                [
                    'contest_id' => $contest->id,
                    'user_id' => $this->assigned_to,
                    'lead_id' => $this->id,
                    'note' => 'Reached "' . $this->statusLabel() . '" — ' . $this->name,
                ],
                [
                    'amount' => $rule->points,
                    'created_by' => null,
                ]
            );
        }
    }
}
