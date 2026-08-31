<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use Illuminate\View\View;

class SuccessStoryController extends Controller
{
    public function index(): View
    {
        $successStories = SuccessStory::visible()->ordered()->paginate(10)->withQueryString();

        return view('user.success-stories.index', [
            'successStories' => $successStories,
        ]);
    }
}
