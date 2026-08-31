<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HallOfFameEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'rank',
        'name',
        'image',
        'description',
        'points',
        'period_start',
        'period_end',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('points')->orderBy('rank');
    }

    /**
     * Entries whose period overlaps the given range. An entry with no period set
     * is treated as always-on (not tied to a specific date range) and always matches.
     */
    public function scopeInPeriod(Builder $query, ?string $from, ?string $to): Builder
    {
        if (! $from && ! $to) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($from, $to) {
            $query->whereNull('period_start')->whereNull('period_end');

            $query->orWhere(function (Builder $query) use ($from, $to) {
                if ($from) {
                    $query->where(fn (Builder $q) => $q->whereNull('period_end')->orWhere('period_end', '>=', $from));
                }
                if ($to) {
                    $query->where(fn (Builder $q) => $q->whereNull('period_start')->orWhere('period_start', '<=', $to));
                }
            });
        });
    }

    public function imageUrl(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function periodLabel(): ?string
    {
        if (! $this->period_start && ! $this->period_end) {
            return null;
        }

        if ($this->period_start && $this->period_end) {
            return $this->period_start->format('d M') . ' – ' . $this->period_end->format('d M Y');
        }

        return ($this->period_start ?? $this->period_end)->format('d M Y');
    }
}
