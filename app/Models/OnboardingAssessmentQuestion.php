<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingAssessmentQuestion extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'type',
        'question_text',
        'points',
        'sort_order',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(OnboardingAssessmentOption::class)->orderBy('sort_order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(OnboardingAssessmentAnswer::class);
    }
}
