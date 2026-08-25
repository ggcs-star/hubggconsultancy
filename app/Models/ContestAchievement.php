<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContestAchievement extends Model
{
    protected $fillable = [
        'contest_id',
        'user_id',
        'lead_id',
        'amount',
        'note',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function contest(): BelongsTo
    {
        return $this->belongsTo(Contest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
