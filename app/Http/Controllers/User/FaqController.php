<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::published()->ordered()->get();

        return view('user.faqs.index', [
            'faqs' => $faqs,
        ]);
    }
}
