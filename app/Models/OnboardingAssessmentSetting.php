<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingAssessmentSetting extends Model
{
    protected $fillable = [
        'title',
        'description',
        'passing_score_percent',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'passing_score_percent' => 'integer',
    ];

    public static function current(): self
    {
        return static::query()->oldest('id')->first() ?? static::create();
    }
}
