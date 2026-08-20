<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnboardingAssessmentOption extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'onboarding_assessment_question_id',
        'option_text',
        'is_correct',
        'sort_order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(OnboardingAssessmentQuestion::class, 'onboarding_assessment_question_id');
    }
}
