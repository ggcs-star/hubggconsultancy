<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PlaceholderController extends Controller
{
    public function manuals(): View
    {
        return view('user.placeholder', [
            'title' => 'Sales Manuals',
            'description' => 'Sales manuals, PPTs and reference documents shared by the team will show up here.',
            'icon' => 'document',
        ]);
    }

    public function socialGuide(): View
    {
        return view('user.placeholder', [
            'title' => 'Social Media Guide',
            'description' => 'Ready-to-share images, videos and captions for your social media promotions.',
            'icon' => 'share',
        ]);
    }
}
