<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuizAnswer;
use App\Services\CoursePlayerPayloadBuilder;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function index(Request $request): View
    {
        $courses = Course::withCount(['modules', 'lessons', 'checkpoints'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->string('search') . '%');
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_published', $request->input('status') === 'published');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.courses.index', [
            'courses' => $courses,
            'pendingReviewCount' => QuizAnswer::whereNull('points_awarded')->whereNotNull('answer_text')->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:5120'],
        ]);

        $data['slug'] = $this->uniqueSlug($data['title']);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->fileUploadService->store($request->file('thumbnail'), 'courses');
        }

        $course = Course::create($data);

        return redirect()->route('admin.courses.show', $course)->with('status', 'Course created.');
    }

    public function show(Request $request, Course $course): View
    {
        $tabs = ['details', 'modules', 'certificate'];
        $activeTab = $request->query('tab', 'details');
        if (! in_array($activeTab, $tabs, true)) {
            $activeTab = 'details';
        }

        $course->load([
            'modules.lessons.checkpoints.questions.options',
            'modules.moduleQuizzes.questions.options',
        ]);

        return view('admin.courses.show', [
            'course' => $course,
            'activeTab' => $activeTab,
        ]);
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('thumbnail')) {
            $this->fileUploadService->delete($course->thumbnail);
            $data['thumbnail'] = $this->fileUploadService->store($request->file('thumbnail'), 'courses');
        }

        $course->update($data);

        return redirect()->route('admin.courses.show', $course)->with('status', 'Course updated.');
    }

    public function updateCertificate(Request $request, Course $course): RedirectResponse
    {
        $data = $request->validate([
            'certificate_background' => ['nullable', 'image', 'max:5120'],
            'fields' => ['nullable', 'array'],
            'fields.*.top' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'fields.*.left' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'fields.*.font_size' => ['nullable', 'integer', 'min:6', 'max:120'],
            'fields.*.color' => ['nullable', 'string', 'max:20'],
        ]);

        $update = ['certificate_fields' => $data['fields'] ?? []];

        if ($request->hasFile('certificate_background')) {
            $this->fileUploadService->delete($course->certificate_background);
            $update['certificate_background'] = $this->fileUploadService->store($request->file('certificate_background'), 'certificates');
        }

        $course->update($update);

        return redirect()
            ->route('admin.courses.show', ['course' => $course, 'tab' => 'certificate'])
            ->with('status', 'Certificate template saved.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->fileUploadService->delete($course->thumbnail);
        $course->delete();

        return redirect()->route('admin.courses.index')->with('status', 'Course deleted.');
    }

    public function togglePublish(Request $request, Course $course): RedirectResponse
    {
        $data = $request->validate([
            'is_published' => ['required', 'boolean'],
        ]);

        $course->update($data);

        return back()->with('status', 'Course status updated.');
    }

    public function preview(Request $request, Course $course, CoursePlayerPayloadBuilder $payloadBuilder): View
    {
        $course->load('modules.lessons.checkpoints.questions', 'modules.moduleQuizzes.questions');

        return view('admin.courses.preview', [
            'course' => $course,
            'modules' => $course->modules,
            'playerData' => $payloadBuilder->build($course, $request->user(), $request->query('item')),
        ]);
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 1;

        while (Course::where('slug', $slug)->exists()) {
            $suffix++;
            $slug = $base . '-' . $suffix;
        }

        return $slug;
    }
}
