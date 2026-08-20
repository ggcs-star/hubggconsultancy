<?php

namespace App\Services;

use App\Models\OnboardingAssessmentOption;
use App\Models\OnboardingAssessmentQuestion;
use App\Models\OnboardingAssessmentQuiz;
use App\Models\OnboardingAssessmentSetting;
use App\Models\User;
use Illuminate\Support\Collection;

class OnboardingAssessmentScorer
{
    /**
     * Combined score across every quiz, plus a per-quiz breakdown.
     *
     * The combined percent pools points across all quizzes (earned / graded,
     * not an average of each quiz's percent), and is compared against the
     * one global OnboardingAssessmentSetting::passing_score_percent.
     */
    public function score(User $user): object
    {
        $settings = OnboardingAssessmentSetting::current();

        $quizzes = $this->relevantQuizzes($user);

        $answersByQuestionId = $user->onboardingAssessmentAnswers()->get()
            ->keyBy('onboarding_assessment_question_id');

        $quizResults = $quizzes->map(
            fn (OnboardingAssessmentQuiz $quiz) => $this->scoreQuiz($quiz, $answersByQuestionId)
        );

        $totalPoints = (int) $quizResults->sum('total_points');
        $gradedPoints = (int) $quizResults->sum('graded_points');
        $earnedPoints = (int) $quizResults->sum('earned_points');
        $correctCount = (int) $quizResults->sum('correct_count');
        $pendingCount = (int) $quizResults->sum('pending_count');
        $questionCount = (int) $quizResults->sum('question_count');

        $attemptedQuizzes = $quizResults->filter(fn (object $q) => $q->attempted);
        $attempted = $attemptedQuizzes->isNotEmpty();
        $allAttempted = $quizzes->isNotEmpty() && $attemptedQuizzes->count() === $quizzes->count();

        $percent = $gradedPoints > 0 ? (int) round($earnedPoints / $gradedPoints * 100) : null;

        $submittedAt = $attemptedQuizzes->pluck('submitted_at')->filter()->min();

        $status = 'not_started';
        if ($attempted && ! $allAttempted) {
            $status = 'in_progress';
        } elseif ($allAttempted) {
            $status = $pendingCount > 0
                ? 'pending_review'
                : ($percent >= $settings->passing_score_percent ? 'passed' : 'failed');
        }

        return (object) [
            'attempted' => $attempted,
            'all_attempted' => $allAttempted,
            'quiz_count' => $quizzes->count(),
            'attempted_quiz_count' => $attemptedQuizzes->count(),
            'submitted_at' => $submittedAt,
            'total_points' => $totalPoints,
            'graded_points' => $gradedPoints,
            'earned_points' => $earnedPoints,
            'percent' => $percent,
            'correct_count' => $correctCount,
            'pending_count' => $pendingCount,
            'question_count' => $questionCount,
            'passing_score_percent' => $settings->passing_score_percent,
            'status' => $status,
            'quizzes' => $quizResults,
        ];
    }

    /**
     * Scores a single quiz for a user against an already-loaded map of their
     * answers (keyed by question id), so a full score() call only queries
     * the answers table once regardless of quiz count.
     */
    public function scoreQuiz(OnboardingAssessmentQuiz $quiz, Collection $answersByQuestionId): object
    {
        $totalPoints = 0;
        $gradedPoints = 0;
        $earnedPoints = 0;
        $correctCount = 0;
        $pendingCount = 0;
        $answeredCount = 0;
        $submittedAt = null;

        foreach ($quiz->questions as $question) {
            $answer = $answersByQuestionId->get($question->id);

            if (! $answer) {
                continue;
            }

            $answeredCount++;
            // Use the point value the question was worth when the user answered it, not
            // whatever it's worth now — a later points edit must not reshape their score.
            $questionPoints = $answer->question_points ?? $question->points;
            $totalPoints += $questionPoints;

            if (! $submittedAt || $answer->created_at->lt($submittedAt)) {
                $submittedAt = $answer->created_at;
            }

            if (is_null($answer->points_awarded)) {
                $pendingCount++;
                continue;
            }

            $gradedPoints += $questionPoints;
            $earnedPoints += $answer->points_awarded;

            if ($answer->is_correct) {
                $correctCount++;
            }
        }

        $percent = $gradedPoints > 0 ? (int) round($earnedPoints / $gradedPoints * 100) : null;
        $attempted = $answeredCount > 0;

        return (object) [
            'quiz' => $quiz,
            'attempted' => $attempted,
            'submitted_at' => $submittedAt,
            'total_points' => $totalPoints,
            'graded_points' => $gradedPoints,
            'earned_points' => $earnedPoints,
            'percent' => $percent,
            'correct_count' => $correctCount,
            'pending_count' => $pendingCount,
            'question_count' => $answeredCount,
            'total_question_count' => $quiz->questions->count(),
            'status' => ! $attempted ? 'not_started' : ($pendingCount > 0 ? 'pending_review' : 'graded'),
        ];
    }

    /**
     * Quizzes relevant to this user: every live (published, non-deleted) quiz/question,
     * plus any quiz/question the user has already answered even if it's since been
     * deleted, unpublished, or edited — so a completed result never changes shape
     * because of a later admin edit. A user with no answers only ever sees live content.
     *
     * @return Collection<int, OnboardingAssessmentQuiz>
     */
    public function relevantQuizzes(User $user, bool $withOptions = false): Collection
    {
        $liveWith = $withOptions ? ['questions.options'] : ['questions'];

        $liveQuizzes = OnboardingAssessmentQuiz::published()->with($liveWith)->ordered()->get();

        $answeredQuestionIds = $user->onboardingAssessmentAnswers()->pluck('onboarding_assessment_question_id');

        $liveQuestionIds = $liveQuizzes->pluck('questions')->flatten()->pluck('id');

        $missingAnsweredQuestions = OnboardingAssessmentQuestion::withTrashed()
            ->whereIn('id', $answeredQuestionIds)
            ->whereNotIn('id', $liveQuestionIds)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('onboarding_assessment_quiz_id');

        $quizzesById = $liveQuizzes->keyBy('id');

        foreach ($missingAnsweredQuestions as $quizId => $questions) {
            $quiz = $quizzesById->get($quizId) ?? OnboardingAssessmentQuiz::withTrashed()->find($quizId);

            if (! $quiz) {
                continue;
            }

            if (! $quizzesById->has($quizId)) {
                $quiz->setRelation('questions', collect());
                $quizzesById->put($quizId, $quiz);
            }

            $quiz->setRelation('questions', $quiz->questions->concat($questions)->sortBy('sort_order')->values());
        }

        if ($withOptions) {
            $answersByQuestionId = $user->onboardingAssessmentAnswers()->get()->keyBy('onboarding_assessment_question_id');

            foreach ($quizzesById as $quiz) {
                foreach ($quiz->questions as $question) {
                    $answer = $answersByQuestionId->get($question->id);

                    if (! $answer) {
                        continue;
                    }

                    // Prefer the exact option list recorded at answer time — options get
                    // wholesale deleted+recreated on every question edit, so falling back
                    // to the live/trashed relation for an un-snapshotted (legacy) answer
                    // can show duplicate-looking rows if the wording didn't actually change.
                    if ($answer->options_snapshot) {
                        $question->setRelation(
                            'options',
                            collect($answer->options_snapshot)->map(fn (array $option) => (object) $option)->values()
                        );
                    } else {
                        // No snapshot recorded (answer predates this fix). Best effort: the
                        // question's current live options, plus whichever exact option was
                        // actually selected even if it's since been trashed by an edit — not
                        // every trashed row ever, which would show stray duplicate choices.
                        $selectedIds = collect($answer->selected_option_ids ?? []);
                        $liveOptions = $question->options()->orderBy('sort_order')->get();
                        $missingSelected = OnboardingAssessmentOption::withTrashed()
                            ->whereIn('id', $selectedIds)
                            ->whereNotIn('id', $liveOptions->pluck('id'))
                            ->get();

                        $question->setRelation(
                            'options',
                            $liveOptions->concat($missingSelected)->sortBy('sort_order')->values()
                        );
                    }
                }
            }
        }

        return $quizzesById->values()->sortBy('sort_order')->values();
    }
}
