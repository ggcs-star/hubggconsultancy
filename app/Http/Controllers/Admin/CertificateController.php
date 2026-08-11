<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(): View
    {
        return view('admin.certificates.index', [
            'courses' => Course::orderBy('title')->get(),
            'certificates' => Certificate::with(['user', 'course'])->latest('issued_at')->paginate(15),
        ]);
    }
}
