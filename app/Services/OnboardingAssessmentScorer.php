<?php

namespace App\Services;

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

        // Draft quizzes aren't reachable by students, so they shouldn't factor into
        // the combined score/pass-fail determination — only affects what's counted,
        // not what admins can see (admin views load their own unfiltered quiz list).
        $quizzes = OnboardingAssessmentQuiz::published()->with('questions')->ordered()->get();

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
            $totalPoints += $question->points;

            if (! $submittedAt || $answer->created_at->lt($submittedAt)) {
                $submittedAt = $answer->created_at;
            }

            if (is_null($answer->points_awarded)) {
                $pendingCount++;
                continue;
            }

            $gradedPoints += $question->points;
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
}
