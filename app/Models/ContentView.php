<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContentView extends Model
{
    protected $fillable = [
        'user_id',
        'viewable_type',
        'viewable_id',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function record(User $user, Model $viewable): void
    {
        static::firstOrCreate([
            'user_id' => $user->id,
            'viewable_type' => $viewable->getMorphClass(),
            'viewable_id' => $viewable->getKey(),
        ], [
            'viewed_at' => now(),
        ]);
    }
}
