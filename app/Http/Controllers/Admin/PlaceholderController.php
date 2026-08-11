<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PlaceholderController extends Controller
{
    public function manuals(): View
    {
        return view('admin.placeholder', [
            'title' => 'Sales Manuals',
            'description' => 'Upload sales manuals, PPTs and documents for your sales team to download.',
            'icon' => 'document',
        ]);
    }

    public function socialGuide(): View
    {
        return view('admin.placeholder', [
            'title' => 'Social Media Guide',
            'description' => 'Manage the images, videos and social media links salespeople use to promote your brand.',
            'icon' => 'share',
        ]);
    }

    public function settings(): View
    {
        return view('admin.placeholder', [
            'title' => 'Settings',
            'description' => 'Platform-wide settings for Pre Sales School will live here.',
            'icon' => 'cog',
        ]);
    }
}
