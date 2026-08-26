<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'date',
        'time',
        'is_completed',
    ];

    protected $casts = [
        'date' => 'date',
        'is_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->where('is_completed', false);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    /** "08/27/2026" style, matching the mockup's date display. */
    public function dateLabel(): string
    {
        return $this->date->format('m/d/Y');
    }

    public function timeLabel(): ?string
    {
        return $this->time ? \Illuminate\Support\Carbon::parse($this->time)->format('g:i A') : null;
    }
}
