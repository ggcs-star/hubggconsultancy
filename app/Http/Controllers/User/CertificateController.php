<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $certificates = Certificate::where('user_id', $request->user()->id)
            ->with('course')
            ->latest('issued_at')
            ->get();

        return view('user.certificates.index', [
            'certificates' => $certificates,
        ]);
    }

    public function show(Request $request, Certificate $certificate): View
    {
        abort_unless($certificate->user_id === $request->user()->id, 403);

        $certificate->load('course', 'user');

        return view('user.certificates.show', [
            'certificate' => $certificate,
        ]);
    }
}
