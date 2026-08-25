<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContestParticipant extends Model
{
    protected $fillable = [
        'contest_id',
        'user_id',
    ];

    public function contest(): BelongsTo
    {
        return $this->belongsTo(Contest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
