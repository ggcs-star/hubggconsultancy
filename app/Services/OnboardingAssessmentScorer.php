<?php

namespace App\Services;

use App\Models\OnboardingAssessmentSetting;
use App\Models\User;

class OnboardingAssessmentScorer
{
    public function score(User $user): object
    {
        $settings = OnboardingAssessmentSetting::current();

        $answers = $user->onboardingAssessmentAnswers()->with('question')->get();

        if ($answers->isEmpty()) {
            return (object) [
                'attempted' => false,
                'submitted_at' => null,
                'total_points' => 0,
                'graded_points' => 0,
                'earned_points' => 0,
                'percent' => null,
                'correct_count' => 0,
                'pending_count' => 0,
                'question_count' => 0,
                'passing_score_percent' => $settings->passing_score_percent,
                'status' => 'not_started',
            ];
        }

        $totalPoints = 0;
        $gradedPoints = 0;
        $earnedPoints = 0;
        $correctCount = 0;
        $pendingCount = 0;

        foreach ($answers as $answer) {
            $totalPoints += $answer->question->points;

            if (is_null($answer->points_awarded)) {
                $pendingCount++;
                continue;
            }

            $gradedPoints += $answer->question->points;
            $earnedPoints += $answer->points_awarded;

            if ($answer->is_correct) {
                $correctCount++;
            }
        }

        $percent = $gradedPoints > 0 ? (int) round($earnedPoints / $gradedPoints * 100) : null;

        $status = 'pending_review';
        if ($pendingCount === 0) {
            $status = $percent >= $settings->passing_score_percent ? 'passed' : 'failed';
        }

        return (object) [
            'attempted' => true,
            'submitted_at' => $answers->min('created_at'),
            'total_points' => $totalPoints,
            'graded_points' => $gradedPoints,
            'earned_points' => $earnedPoints,
            'percent' => $percent,
            'correct_count' => $correctCount,
            'pending_count' => $pendingCount,
            'question_count' => $answers->count(),
            'passing_score_percent' => $settings->passing_score_percent,
            'status' => $status,
        ];
    }
}
