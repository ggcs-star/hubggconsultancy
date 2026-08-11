<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PlaceholderController extends Controller
{
    public function training(): View
    {
        return view('user.placeholder', [
            'title' => 'Training / LMS',
            'description' => 'Your assigned training courses will appear here once the curriculum is published.',
            'icon' => 'academic-cap',
        ]);
    }

    public function certificates(): View
    {
        return view('user.placeholder', [
            'title' => 'Certificates',
            'description' => 'Certificates you earn after completing training courses will be listed here.',
            'icon' => 'badge',
        ]);
    }

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
