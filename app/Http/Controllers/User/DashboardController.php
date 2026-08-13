<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CourseLessonProgress;
use App\Models\QuizAnswer;
use App\Services\OnboardingAssessmentScorer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, OnboardingAssessmentScorer $scorer): View
    {
        $user = $request->user();
        $courses = $user->assignedCourses;

        $courseProgress = $courses->map(function ($course) use ($user) {
            $lessonIds = $course->lessons()->pluck('course_lessons.id');
            $totalLessons = $lessonIds->count();
            $completedLessons = CourseLessonProgress::where('user_id', $user->id)
                ->whereIn('course_lesson_id', $lessonIds)
                ->where('completed', true)
                ->count();

            $percent = $totalLessons > 0 ? (int) round($completedLessons / $totalLessons * 100) : 0;

            return (object) [
                'course' => $course,
                'modules_count' => $course->modules()->count(),
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'percent' => $percent,
                'status' => $percent === 0 ? 'Not started' : ($percent === 100 ? 'Completed' : 'In progress'),
            ];
        });

        $totalLessons = $courseProgress->sum('total_lessons');
        $completedLessons = $courseProgress->sum('completed_lessons');

        $stats = [
            'enrolled_courses' => $courses->count(),
            'overall_completion' => $totalLessons > 0 ? (int) round($completedLessons / $totalLessons * 100) : 0,
            'certificates' => Certificate::where('user_id', $user->id)->count(),
            'assessment_percent' => $scorer->score($user)->percent ?? 0,
        ];

        return view('user.dashboard', [
            'user' => $user,
            'assessmentScore' => $scorer->score($user),
            'stats' => $stats,
            'courseProgress' => $courseProgress,
            'progressChart' => [
                'labels' => $courseProgress->map(fn ($cp) => \Illuminate\Support\Str::limit($cp->course->title, 18))->all(),
                'data' => $courseProgress->pluck('percent')->all(),
            ],
            'activityChart' => $this->activityChart($user->id),
            'quizChart' => $this->quizChart($user->id),
        ]);
    }

    /**
     * Lessons completed per day over the last 14 days, zero-filled.
     */
    private function activityChart(int $userId): array
    {
        $start = now()->subDays(13)->startOfDay();

        $counts = CourseLessonProgress::where('user_id', $userId)
            ->where('completed', true)
            ->where('completed_at', '>=', $start)
            ->get()
            ->groupBy(fn ($progress) => $progress->completed_at->format('Y-m-d'))
            ->map->count();

        $labels = [];
        $data = [];

        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $key = $day->format('Y-m-d');
            $labels[] = $day->format('d M');
            $data[] = (int) ($counts[$key] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function quizChart(int $userId): array
    {
        $answers = QuizAnswer::where('user_id', $userId)->get();

        return [
            'labels' => ['Correct', 'Incorrect', 'Pending Review'],
            'data' => [
                $answers->where('is_correct', true)->count(),
                $answers->where('is_correct', false)->count(),
                $answers->whereNull('is_correct')->count(),
            ],
        ];
    }
}
