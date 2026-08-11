<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\CourseQuizCheckpoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CourseQuizCheckpointController extends Controller
{
    public function store(Request $request, CourseLesson $lesson): RedirectResponse
    {
        $data = $this->validateCheckpoint($request, $lesson);

        CourseQuizCheckpoint::create([
            'course_id' => $lesson->module->course_id,
            'course_module_id' => $lesson->course_module_id,
            'course_lesson_id' => $lesson->id,
            'timestamp_seconds' => $data['timestamp_seconds'],
            'title' => $data['title'],
        ]);

        return redirect()->route('admin.course-lessons.edit', $lesson)->with('status', 'Checkpoint added.');
    }

    public function update(Request $request, CourseQuizCheckpoint $checkpoint): RedirectResponse
    {
        $data = $this->validateCheckpoint($request, $checkpoint->lesson);

        $checkpoint->update([
            'timestamp_seconds' => $data['timestamp_seconds'],
            'title' => $data['title'],
        ]);

        return redirect()->route('admin.course-lessons.edit', $checkpoint->course_lesson_id)->with('status', 'Checkpoint updated.');
    }

    public function destroy(CourseQuizCheckpoint $checkpoint): RedirectResponse
    {
        $lessonId = $checkpoint->course_lesson_id;
        $checkpoint->delete();

        return redirect()->route('admin.course-lessons.edit', $lessonId)->with('status', 'Checkpoint deleted.');
    }

    private function validateCheckpoint(Request $request, CourseLesson $lesson): array
    {
        $validated = $request->validate([
            'minutes' => ['required', 'integer', 'min:0'],
            'seconds' => ['required', 'integer', 'min:0', 'max:59'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $timestampSeconds = ($validated['minutes'] * 60) + $validated['seconds'];
        $durationSeconds = $lesson->durationSeconds();

        if ($durationSeconds !== null && $timestampSeconds > $durationSeconds) {
            throw ValidationException::withMessages([
                'minutes' => "Checkpoint time can't be past the lesson's duration ({$lesson->duration}).",
            ]);
        }

        return [
            'timestamp_seconds' => $timestampSeconds,
            'title' => $validated['title'] ?? null,
        ];
    }
}
