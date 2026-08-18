<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingAssessmentQuestion extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'onboarding_assessment_quiz_id',
        'type',
        'question_text',
        'points',
        'sort_order',
    ];

    protected static function booted(): void
    {
        // This app's tables are MyISAM (no FK support), so ON DELETE CASCADE in the
        // migrations is not actually enforced by the database — cascade manually.
        static::deleting(function (self $question) {
            $question->options()->delete();
            $question->answers()->delete();
        });
    }

    public function sortOrderScopeColumn(): string
    {
        return 'onboarding_assessment_quiz_id';
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(OnboardingAssessmentQuiz::class, 'onboarding_assessment_quiz_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(OnboardingAssessmentOption::class)->orderBy('sort_order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(OnboardingAssessmentAnswer::class);
    }
}
