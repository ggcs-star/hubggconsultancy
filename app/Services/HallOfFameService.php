<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\ContestAchievement;
use App\Models\CourseLessonProgress;
use App\Models\Lead;
use App\Models\QuizAnswer;
use App\Models\ResourceQuizAnswer;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class HallOfFameService
{
    /**
     * One row per non-admin user with every leaderboard metric pre-computed
     * via bulk grouped queries (not per-user loops), then merged in PHP —
     * keeps this to a handful of queries regardless of user count.
     */
    public function rankedUsers(?Carbon $from, ?Carbon $to): Collection
    {
        $users = User::where('role', 'user')->get(['id', 'name', 'designation']);

        $points = $this->pointsInPeriod($from, $to);
        $learningScores = $this->learningScorePercentByUser();

        $certCounts = Certificate::selectRaw('user_id, COUNT(*) as cnt')
            ->groupBy('user_id')
            ->pluck('cnt', 'user_id');

        $leadsWon = Lead::selectRaw('assigned_to as user_id, COUNT(*) as cnt')
            ->where('status', 'won')
            ->when($from, fn ($q) => $q->where('won_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('won_at', '<=', $to))
            ->groupBy('assigned_to')
            ->pluck('cnt', 'user_id');

        $activeDays = CourseLessonProgress::selectRaw('user_id, COUNT(DISTINCT DATE(completed_at)) as days')
            ->where('completed', true)
            ->when($from, fn ($q) => $q->where('completed_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('completed_at', '<=', $to))
            ->groupBy('user_id')
            ->pluck('days', 'user_id');

        return $users->map(fn (User $user) => (object) [
            'user' => $user,
            'points' => (int) ($points[$user->id] ?? 0),
            'certificates' => (int) ($certCounts[$user->id] ?? 0),
            'leadsWon' => (int) ($leadsWon[$user->id] ?? 0),
            'activeDays' => (int) ($activeDays[$user->id] ?? 0),
            'learningScore' => isset($learningScores[$user->id]) ? (int) $learningScores[$user->id] : null,
        ]);
    }

    /** @return array<int, int> points by user_id */
    private function pointsInPeriod(?Carbon $from, ?Carbon $to): array
    {
        $lms = QuizAnswer::query()
            ->join('course_quiz_questions', 'course_quiz_questions.id', '=', 'client_quiz_answers.quiz_question_id')
            ->whereNotNull('client_quiz_answers.points_awarded')
            ->when($from, fn ($q) => $q->where('client_quiz_answers.graded_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('client_quiz_answers.graded_at', '<=', $to))
            ->selectRaw('client_quiz_answers.user_id as user_id, SUM(client_quiz_answers.points_awarded) as pts')
            ->groupBy('client_quiz_answers.user_id')
            ->pluck('pts', 'user_id');

        $resource = ResourceQuizAnswer::query()
            ->whereNotNull('points_awarded')
            ->when($from, fn ($q) => $q->where('graded_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('graded_at', '<=', $to))
            ->selectRaw('user_id, SUM(points_awarded) as pts')
            ->groupBy('user_id')
            ->pluck('pts', 'user_id');

        $contest = ContestAchievement::query()
            ->whereHas('contest', fn ($q) => $q->where('achievement_source', 'crm'))
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->selectRaw('user_id, SUM(amount) as pts')
            ->groupBy('user_id')
            ->pluck('pts', 'user_id');

        $combined = [];
        foreach ([$lms, $resource, $contest] as $bucket) {
            foreach ($bucket as $userId => $pts) {
                $combined[$userId] = ($combined[$userId] ?? 0) + (int) $pts;
            }
        }

        return $combined;
    }

    /**
     * Weighted average LMS quiz score (earned / assigned points) per user
     * across every graded question — the "Highest Learning Score" metric.
     * All-time by design: a period filter here would penalize someone with
     * a strong track record who simply didn't take a quiz this month.
     */
    private function learningScorePercentByUser(): Collection
    {
        return QuizAnswer::query()
            ->join('course_quiz_questions', 'course_quiz_questions.id', '=', 'client_quiz_answers.quiz_question_id')
            ->whereNotNull('client_quiz_answers.points_awarded')
            ->selectRaw('client_quiz_answers.user_id as user_id, SUM(client_quiz_answers.points_awarded) as earned, SUM(course_quiz_questions.points) as total')
            ->groupBy('client_quiz_answers.user_id')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->user_id => $row->total > 0 ? (int) round($row->earned / $row->total * 100) : null]);
    }
}
