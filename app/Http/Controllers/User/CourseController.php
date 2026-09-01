<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseLessonProgress;
use App\Models\QuizAnswer;
use App\Services\CoursePlayerPayloadBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isInactive()) {
            return redirect()->route('user.dashboard')->with('status', 'Your account is inactive. Contact your administrator for course access.');
        }

        $courses = $user->assignedCourses()
            ->where('is_published', true)
            ->withCount('lessons')
            ->orderBy('title')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($course) use ($user) {
                $course->score = $course->scoreFor($user);

                return $course;
            });

        return view('user.courses.index', [
            'courses' => $courses,
        ]);
    }

    public function show(Request $request, Course $course, CoursePlayerPayloadBuilder $payloadBuilder): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isInactive()) {
            return redirect()->route('user.dashboard')->with('status', 'Your account is inactive. Contact your administrator for course access.');
        }

        abort_unless($course->is_published, 404);
        abort_unless($user->assignedCourses()->where('courses.id', $course->id)->exists(), 404);

        $course->load('modules.lessons.checkpoints.questions', 'modules.moduleQuizzes.questions');

        $completed = $this->isCompletedBy($course, $user);
        $certificate = $completed ? $this->issueCertificateIfEligible($course, $user) : null;

        return view('user.courses.show', [
            'course' => $course,
            'modules' => $course->modules,
            'playerData' => $payloadBuilder->build($course, $user, $request->query('item')),
            'completed' => $completed,
            'score' => $course->scoreFor($user),
            'certificate' => $certificate,
        ]);
    }

    private function issueCertificateIfEligible(Course $course, $user): ?Certificate
    {
        if (! $course->hasCertificateTemplate()) {
            return null;
        }

        return Certificate::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['certificate_number' => Certificate::generateNumber(), 'issued_at' => now()]
        );
    }

    private function isCompletedBy(Course $course, $user): bool
    {
        $lessonIds = $course->modules->flatMap->lessons->pluck('id');

        if ($lessonIds->isEmpty()) {
            return false;
        }

        $completedLessonCount = CourseLessonProgress::where('user_id', $user->id)
            ->where('completed', true)
            ->whereIn('course_lesson_id', $lessonIds)
            ->count();

        if ($completedLessonCount < $lessonIds->count()) {
            return false;
        }

        $requiredQuestionIds = $course->modules->flatMap->moduleQuizzes
            ->where('is_required', true)
            ->flatMap->questions
            ->pluck('id');

        if ($requiredQuestionIds->isEmpty()) {
            return true;
        }

        $answeredCount = QuizAnswer::where('user_id', $user->id)
            ->whereIn('quiz_question_id', $requiredQuestionIds)
            ->count();

        return $answeredCount >= $requiredQuestionIds->count();
    }
}
