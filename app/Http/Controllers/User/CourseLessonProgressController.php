<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\CourseLessonProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseLessonProgressController extends Controller
{
    public function store(Request $request, CourseLesson $lesson): JsonResponse
    {
        $data = $request->validate([
            'last_position_seconds' => ['nullable', 'integer', 'min:0'],
            'completed' => ['nullable', 'boolean'],
        ]);

        $completed = $request->boolean('completed');

        CourseLessonProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'course_lesson_id' => $lesson->id],
            array_filter([
                'last_position_seconds' => $data['last_position_seconds'] ?? 0,
                'completed' => $completed,
                'completed_at' => $completed ? now() : null,
            ], fn ($value) => ! is_null($value))
        );

        return response()->json(['status' => 'ok']);
    }
}
