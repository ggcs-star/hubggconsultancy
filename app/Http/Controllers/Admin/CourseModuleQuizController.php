<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseModule;
use App\Models\CourseQuizCheckpoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseModuleQuizController extends Controller
{
    public function store(Request $request, CourseModule $module): RedirectResponse
    {
        $data = $this->validateQuiz($request, $module);

        CourseQuizCheckpoint::create([
            'course_id' => $module->course_id,
            'course_module_id' => $module->id,
            'course_lesson_id' => null,
            'after_course_lesson_id' => $data['after_course_lesson_id'],
            'title' => $data['title'],
            'is_required' => $data['is_required'],
        ]);

        return redirect()
            ->route('admin.courses.show', ['course' => $module->course_id, 'tab' => 'modules'])
            ->with('status', 'Module quiz added.');
    }

    public function edit(CourseQuizCheckpoint $quiz): View
    {
        $quiz->load('questions.options');

        return view('admin.courses.module-quizzes.edit', [
            'quiz' => $quiz,
            'module' => $quiz->module,
        ]);
    }

    public function update(Request $request, CourseQuizCheckpoint $quiz): RedirectResponse
    {
        $data = $this->validateQuiz($request, $quiz->module);

        $quiz->update([
            'after_course_lesson_id' => $data['after_course_lesson_id'],
            'title' => $data['title'],
            'is_required' => $data['is_required'],
        ]);

        return redirect()->route('admin.course-module-quizzes.edit', $quiz)->with('status', 'Quiz updated.');
    }

    public function destroy(CourseQuizCheckpoint $quiz): RedirectResponse
    {
        $courseId = $quiz->course_id;
        $quiz->delete();

        return redirect()
            ->route('admin.courses.show', ['course' => $courseId, 'tab' => 'modules'])
            ->with('status', 'Module quiz deleted.');
    }

    private function validateQuiz(Request $request, CourseModule $module): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'after_course_lesson_id' => ['nullable', 'integer', 'exists:course_lessons,id'],
            'is_required' => ['nullable', 'boolean'],
        ]);

        return [
            'title' => $validated['title'],
            'after_course_lesson_id' => $validated['after_course_lesson_id'] ?? null,
            'is_required' => $request->boolean('is_required'),
        ];
    }
}
