<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseLessonController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function create(CourseModule $courseModule): View
    {
        return view('admin.courses.lessons.create', ['courseModule' => $courseModule]);
    }

    public function store(Request $request, CourseModule $courseModule): RedirectResponse
    {
        $data = $this->validateLesson($request);
        $data['course_module_id'] = $courseModule->id;

        if ($data['video_source'] === 'upload' && $request->hasFile('video')) {
            $data['video_path'] = $this->fileUploadService->store($request->file('video'), 'course-lessons');
        }

        $lesson = CourseLesson::create($data);

        return redirect()->route('admin.course-lessons.edit', $lesson)->with('status', 'Lesson created — add quiz checkpoints below.');
    }

    public function edit(CourseLesson $lesson): View
    {
        $lesson->load('checkpoints.questions.options', 'module.course');

        return view('admin.courses.lessons.edit', [
            'lesson' => $lesson,
            'courseModule' => $lesson->module,
        ]);
    }

    public function update(Request $request, CourseLesson $lesson): RedirectResponse
    {
        $data = $this->validateLesson($request);

        if ($data['video_source'] === 'upload' && $request->hasFile('video')) {
            $this->fileUploadService->delete($lesson->video_path);
            $data['video_path'] = $this->fileUploadService->store($request->file('video'), 'course-lessons');
        }

        $lesson->update($data);

        return redirect()->route('admin.course-lessons.edit', $lesson)->with('status', 'Lesson updated.');
    }

    public function destroy(CourseLesson $lesson): RedirectResponse
    {
        $courseId = $lesson->module->course_id;
        $this->fileUploadService->delete($lesson->video_path);
        $lesson->delete();

        return redirect()
            ->route('admin.courses.show', ['course' => $courseId, 'tab' => 'modules'])
            ->with('status', 'Lesson deleted.');
    }

    private function validateLesson(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'video_source' => ['required', 'in:upload,youtube'],
            'video' => ['nullable', 'file', 'mimes:mp4,webm,ogg', 'max:102400'],
            'video_url' => ['nullable', 'string', 'max:2000'],
            'duration' => ['nullable', 'string', 'max:20'],
        ]);

        // The textarea accepts either a plain URL or a full pasted <iframe> embed —
        // CourseLesson::youtubeId() extracts the video ID out of either form, so
        // the raw text is stored as-is.
        return $data;
    }
}
