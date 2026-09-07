<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingChecklistItem extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'title',
        'description',
        'link',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        // This app's tables are MyISAM (no FK support), so ON DELETE CASCADE in
        // the migration is not actually enforced by the database — cascade manually.
        static::deleting(function (self $item) {
            $item->completions()->delete();
        });
    }

    public function completions(): HasMany
    {
        return $this->hasMany(OnboardingChecklistCompletion::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Cross-checks this checklist item against real completion data before
     * a user is allowed to self-tick it, by best-effort title-matching
     * against a published Course (Training / LMS), Resource (Live &
     * Recorded Training), or OnboardingAssessmentQuiz (Assessments), in
     * that priority order. There's no explicit admin-configured link
     * between a checklist item and any of these — matching strips a
     * trailing status word ("Done"/"Completed"/etc.) and compares
     * case-insensitively (exact or substring).
     *
     * Returns:
     *   - true  => matched something, and the user has genuinely completed it
     *   - false => matched something, but the user hasn't finished it yet —
     *              the self-tick should be blocked
     *   - null  => nothing in Courses/Resources/Assessments reasonably
     *              matches this item's title (e.g. "100 Prospect List
     *              Submitted"), so there's nothing to verify — it stays a
     *              plain, unblockable self-reported tick, same as always
     */
    public function verifyCompletionFor(User $user): ?bool
    {
        if ($course = $this->matchAgainst(Course::where('is_published', true)->get(['id', 'title']))) {
            return $course->progressFor($user)->percent === 100;
        }

        if ($resource = $this->matchAgainst(Resource::where('is_published', true)->get(['id', 'title']))) {
            $questionIds = $resource->checkpoints()->with('questions')->get()->flatMap->questions->pluck('id');

            // No quiz on this resource means no way to verify "watched" —
            // fall through to "nothing to verify" rather than making it
            // permanently unblockable.
            if ($questionIds->isEmpty()) {
                return null;
            }

            $answeredIds = ResourceQuizAnswer::where('user_id', $user->id)
                ->whereIn('resource_quiz_question_id', $questionIds)
                ->pluck('resource_quiz_question_id');

            return $questionIds->diff($answeredIds)->isEmpty();
        }

        if ($quiz = $this->matchAgainst(OnboardingAssessmentQuiz::published()->get(['id', 'title']))) {
            $questionIds = $quiz->questions()->pluck('id');

            if ($questionIds->isEmpty()) {
                return null;
            }

            $answeredIds = $user->onboardingAssessmentAnswers()
                ->whereIn('onboarding_assessment_question_id', $questionIds)
                ->pluck('onboarding_assessment_question_id');

            return $questionIds->diff($answeredIds)->isEmpty();
        }

        return null;
    }

    private function matchAgainst(\Illuminate\Support\Collection $candidates): ?Model
    {
        $needle = self::normalizeTitleForMatching($this->title);

        if ($needle === '') {
            return null;
        }

        foreach ($candidates as $candidate) {
            $haystack = self::normalizeTitleForMatching($candidate->title);

            if ($haystack !== '' && ($needle === $haystack || str_contains($needle, $haystack) || str_contains($haystack, $needle))) {
                return $candidate;
            }
        }

        return null;
    }

    private static function normalizeTitleForMatching(string $value): string
    {
        return trim(preg_replace(
            '/\b(done|completed|finished|submitted)\b/i',
            '',
            strtolower(preg_replace('/[^a-z0-9\s]/i', ' ', $value))
        ));
    }
}
