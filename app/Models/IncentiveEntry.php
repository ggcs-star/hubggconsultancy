<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncentiveEntry extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'amount',
        'type',
        'source',
        'contest_id',
        'awarded_at',
        'note',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'awarded_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contest(): BelongsTo
    {
        return $this->belongsTo(Contest::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'points' => 'Points',
            'bonus' => 'Bonus',
            'cash' => 'Cash',
            'gift' => 'Gift',
            default => ucfirst($this->type),
        };
    }
}
