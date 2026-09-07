<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class RankMedal extends Model
{
    protected $fillable = [
        'rank',
        'emoji',
    ];

    /**
     * rank => emoji, e.g. [1 => '🥇', 2 => '🥈', 3 => '🥉'] — cached
     * indefinitely since this data only ever changes via the admin edit
     * form (which busts the cache itself), and the leaderboard partial
     * calls this once per row, so a live query per row would be wasteful.
     */
    public static function map(): array
    {
        return Cache::rememberForever('rank_medals', fn () => static::orderBy('rank')->pluck('emoji', 'rank')->all());
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('rank_medals'));
        static::deleted(fn () => Cache::forget('rank_medals'));
    }
}
