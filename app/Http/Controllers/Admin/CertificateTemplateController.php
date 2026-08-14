<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use App\Models\Course;
use Illuminate\Http\Request;

class CertificateTemplateController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Template List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $templates = CertificateTemplate::query()
            ->withCount('courses')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view(
            'admin.certificates.templates.index',
            compact('templates')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Course Template Assignment Page
    |--------------------------------------------------------------------------
    */

    public function assign(Course $course)
    {
        $templates = CertificateTemplate::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view(
            'admin.certificates.templates.assign',
            compact(
                'course',
                'templates'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Assign Template To Course
    |--------------------------------------------------------------------------
    */

    public function updateAssignment(
        Request $request,
        Course $course
    ) {
        $validated = $request->validate([
            'certificate_template_id' => [
                'required',
                'exists:certificate_templates,id',
            ],
        ]);

        $template = CertificateTemplate::query()
            ->where('id', $validated['certificate_template_id'])
            ->where('is_active', true)
            ->first();

        if (! $template) {
            return back()
                ->withInput()
                ->withErrors([
                    'certificate_template_id' =>
                        'Selected certificate template is not available.',
                ]);
        }

        $course->update([
            'certificate_template_id' => $template->id,
        ]);

        return redirect()
            ->route(
                'admin.certificates.templates.assign',
                $course
            )
            ->with(
                'success',
                'Certificate template assigned successfully.'
            );
    }
}