<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\CourseQuizCheckpoint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseModuleController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $data['course_id'] = $course->id;

        CourseModule::create($data);

        return redirect()
            ->route('admin.courses.show', ['course' => $course, 'tab' => 'modules'])
            ->with('status', 'Module added.');
    }

    public function update(Request $request, CourseModule $module): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $module->update($data);

        return redirect()
            ->route('admin.courses.show', ['course' => $module->course_id, 'tab' => 'modules'])
            ->with('status', 'Module updated.');
    }

    public function destroy(CourseModule $module): RedirectResponse
    {
        $courseId = $module->course_id;
        $module->delete();

        return redirect()
            ->route('admin.courses.show', ['course' => $courseId, 'tab' => 'modules'])
            ->with('status', 'Module deleted.');
    }

    /**
     * Reorders modules within a course. Body: { ids: [moduleId, moduleId, ...] }
     */
    public function reorder(Request $request, Course $course): JsonResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array']])['ids'];

        foreach ($ids as $index => $id) {
            CourseModule::where('id', $id)->where('course_id', $course->id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Reorders lessons + module quizzes together within a module.
     * Body: { ids: ["lesson-5", "quiz-2", "lesson-6", ...] }
     */
    public function reorderItems(Request $request, CourseModule $module): JsonResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array']])['ids'];

        foreach ($ids as $index => $itemKey) {
            [$type, $id] = explode('-', $itemKey, 2);

            if ($type === 'lesson') {
                CourseLesson::where('id', $id)->where('course_module_id', $module->id)->update(['sort_order' => $index + 1]);
            } elseif ($type === 'quiz') {
                CourseQuizCheckpoint::where('id', $id)->where('course_module_id', $module->id)->update(['sort_order' => $index + 1]);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
