<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_number',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeIssuedBetween(Builder $query, ?Carbon $from, ?Carbon $to): Builder
    {
        return $query
            ->when($from, fn ($q) => $q->where('issued_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('issued_at', '<=', $to));
    }

    /**
     * Score-based badge shown on Achiever cards — thresholds mirror the
     * "Excellence / Great Job / Completed" tiers requested for the feed.
     */
    public static function badgeFor(?int $percent): array
    {
        return match (true) {
            $percent >= 90 => ['label' => 'Excellence', 'emoji' => '🏆', 'class' => 'badge-green'],
            $percent >= 75 => ['label' => 'Great Job', 'emoji' => '⭐', 'class' => 'badge-amber'],
            default => ['label' => 'Completed', 'emoji' => '✅', 'class' => 'badge-slate'],
        };
    }

    public static function generateNumber(): string
    {
        do {
            $number = 'PSS-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        } while (static::where('certificate_number', $number)->exists());

        return $number;
    }
}
