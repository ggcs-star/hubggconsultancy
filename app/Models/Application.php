<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $table = 'applications';

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'referral_mobile',
            'phone'
        );
    }
}
