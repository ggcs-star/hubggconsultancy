<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContestPointRule extends Model
{
    protected $fillable = [
        'contest_id',
        'lead_status',
        'points',
    ];

    public function contest(): BelongsTo
    {
        return $this->belongsTo(Contest::class);
    }

    public function statusLabel(): string
    {
        return Lead::statusLabels()[$this->lead_status] ?? ucfirst($this->lead_status);
    }
}
