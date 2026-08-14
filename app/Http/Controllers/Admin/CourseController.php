<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
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
    public function __construct(
        private FileUploadService $fileUploadService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Course List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $courses = Course::withCount([
            'modules',
            'lessons',
            'checkpoints',
        ])
            ->when(
                $request->filled('search'),
                function ($query) use ($request) {
                    $query->where(
                        'title',
                        'like',
                        '%' . $request->string('search') . '%'
                    );
                }
            )
            ->when(
                $request->filled('status'),
                function ($query) use ($request) {
                    $query->where(
                        'is_published',
                        $request->input('status') === 'published'
                    );
                }
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.courses.index', [
            'courses' => $courses,

            'pendingReviewCount' => QuizAnswer::whereNull(
                'points_awarded'
            )
                ->whereNotNull('answer_text')
                ->count(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Create Course
    |--------------------------------------------------------------------------
    */

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'thumbnail' => [
                'nullable',
                'image',
                'max:5120',
            ],
        ]);

        $data['slug'] = $this->uniqueSlug(
            $data['title']
        );

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->fileUploadService->store(
                $request->file('thumbnail'),
                'courses'
            );
        }

        $course = Course::create($data);

        return redirect()
            ->route(
                'admin.courses.show',
                $course
            )
            ->with(
                'status',
                'Course created.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Course Details
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        Course $course
    ): View {

        /*
        |--------------------------------------------------------------------------
        | Available Tabs
        |--------------------------------------------------------------------------
        */

        $tabs = [
            'details',
            'modules',
            'certificate',
        ];

        $activeTab = $request->query(
            'tab',
            'details'
        );

        if (! in_array(
            $activeTab,
            $tabs,
            true
        )) {
            $activeTab = 'details';
        }


        /*
        |--------------------------------------------------------------------------
        | Load Course Relations
        |--------------------------------------------------------------------------
        */

        $course->load([
            'modules.lessons.checkpoints.questions.options',
            'modules.moduleQuizzes.questions.options',
            'certificateTemplate',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Certificate Templates
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | Only active certificate templates are shown.
        |
        | These templates are shared between multiple courses.
        |
        */

        $templates = CertificateTemplate::query()
            ->where('is_active', true)
            ->withCount('courses')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.courses.show',
            [
                'course' => $course,

                'activeTab' => $activeTab,

                'templates' => $templates,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Course
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Course $course
    ): RedirectResponse {

        $data = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'thumbnail' => [
                'nullable',
                'image',
                'max:5120',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Replace Thumbnail
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('thumbnail')) {

            $this->fileUploadService->delete(
                $course->thumbnail
            );

            $data['thumbnail'] =
                $this->fileUploadService->store(
                    $request->file('thumbnail'),
                    'courses'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $course->update($data);


        return redirect()
            ->route(
                'admin.courses.show',
                $course
            )
            ->with(
                'status',
                'Course updated.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Certificate Template
    |--------------------------------------------------------------------------
    */

    public function updateCertificate(
        Request $request,
        Course $course
    ): RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | Validate Template
        |--------------------------------------------------------------------------
        */

        $data = $request->validate([
            'certificate_template_id' => [
                'required',
                'integer',
                'exists:certificate_templates,id',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Check Template Is Active
        |--------------------------------------------------------------------------
        */

        $template = CertificateTemplate::query()
            ->whereKey(
                $data['certificate_template_id']
            )
            ->where('is_active', true)
            ->first();


        if (! $template) {

            return back()
                ->withErrors([
                    'certificate_template_id' =>
                        'The selected certificate template is not active.',
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | Assign Template To Course
        |--------------------------------------------------------------------------
        */

        $course->update([
            'certificate_template_id' =>
                $template->id,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'admin.courses.show',
                [
                    'course' => $course,
                    'tab' => 'certificate',
                ]
            )
            ->with(
                'success',
                $template->name .
                ' certificate template assigned successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Course
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Course $course
    ): RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | Delete Thumbnail
        |--------------------------------------------------------------------------
        */

        $this->fileUploadService->delete(
            $course->thumbnail
        );


        /*
        |--------------------------------------------------------------------------
        | Delete Course
        |--------------------------------------------------------------------------
        */

        $course->delete();


        return redirect()
            ->route(
                'admin.courses.index'
            )
            ->with(
                'status',
                'Course deleted.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Publish / Unpublish Course
    |--------------------------------------------------------------------------
    */

    public function togglePublish(
        Request $request,
        Course $course
    ): RedirectResponse {

        $data = $request->validate([
            'is_published' => [
                'required',
                'boolean',
            ],
        ]);


        $course->update($data);


        return back()->with(
            'status',
            'Course status updated.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Course Preview
    |--------------------------------------------------------------------------
    */

    public function preview(
        Request $request,
        Course $course,
        CoursePlayerPayloadBuilder $payloadBuilder
    ): View {

        $course->load([
            'modules.lessons.checkpoints.questions',
            'modules.moduleQuizzes.questions',
            'certificateTemplate',
        ]);


        return view(
            'admin.courses.preview',
            [
                'course' => $course,

                'modules' => $course->modules,

                'playerData' =>
                    $payloadBuilder->build(
                        $course,
                        $request->user(),
                        $request->query('item')
                    ),
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Generate Unique Course Slug
    |--------------------------------------------------------------------------
    */

    private function uniqueSlug(
        string $title
    ): string {

        $base = Str::slug($title);

        $slug = $base;

        $suffix = 1;


        while (
            Course::where(
                'slug',
                $slug
            )->exists()
        ) {

            $suffix++;

            $slug =
                $base .
                '-' .
                $suffix;
        }


        return $slug;
    }
}