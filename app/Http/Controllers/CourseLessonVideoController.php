<?php

namespace App\Http\Controllers;

use App\Models\CourseLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CourseLessonVideoController extends Controller
{
    /**
     * Streams the uploaded lesson video through Laravel (rather than the
     * public/storage symlink) so Symfony's BinaryFileResponse can serve
     * HTTP Range requests — required for <video> playback/seeking, and
     * not supported by PHP's built-in `php artisan serve` static file
     * handler when the file is served directly via the symlink.
     */
    public function show(Request $request, CourseLesson $lesson): BinaryFileResponse
    {
        abort_unless($lesson->video_source === 'upload' && $lesson->video_path, 404);

        $user = $request->user();
        $course = $lesson->module->course;

        abort_unless(
            $user->role === 'admin' || $user->assignedCourses()->where('courses.id', $course->id)->exists(),
            403
        );

        abort_unless(Storage::disk('public')->exists($lesson->video_path), 404);

        return response()->file(Storage::disk('public')->path($lesson->video_path));
    }
}
