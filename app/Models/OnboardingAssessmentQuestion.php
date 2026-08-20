<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnboardingAssessmentQuestion extends Model
{
    use HasFactory, HasSortOrder, SoftDeletes;

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
        // Answers are deliberately never touched here: once a user has answered a
        // question, deleting/editing it must not affect their already-submitted result.
        static::deleting(function (self $question) {
            $question->options()->delete();
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
