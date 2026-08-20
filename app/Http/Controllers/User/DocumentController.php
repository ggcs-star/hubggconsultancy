<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(): View
    {
        return view('user.documents.index', [
            'documents' => Document::published()->ordered()->get(),
        ]);
    }
}
