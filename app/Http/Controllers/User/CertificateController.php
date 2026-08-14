<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateController extends Controller
{
    /**
     * User: Show own certificates
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $certificates = Certificate::query()
            ->where('user_id', $user->id)
            ->with([
                'course.certificateTemplate',
            ])
            ->latest('issued_at')
            ->get();

        return view(
            'user.certificates.index',
            [
                'certificates' => $certificates,
            ]
        );
    }


    /**
     * User: Show own certificate
     */
    public function show(
        Request $request,
        Certificate $certificate
    ): View {
        /*
        |--------------------------------------------------------------------------
        | Security
        |--------------------------------------------------------------------------
        |
        | User can only view their own certificate.
        |
        */

        abort_unless(
            $certificate->user_id === $request->user()->id,
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Load Certificate Data
        |--------------------------------------------------------------------------
        */

        $certificate->load([
            'course.certificateTemplate',
            'user',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Assigned Template
        |--------------------------------------------------------------------------
        |
        | Only the template assigned to this certificate's course
        | will be loaded.
        |
        */

        $template = $certificate
            ->course
            ->certificateTemplate;


        return view(
            'user.certificates.show',
            [
                'certificate' => $certificate,
                'template' => $template,
            ]
        );
    }
}