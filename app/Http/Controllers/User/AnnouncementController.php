<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::visible()->latest('published_at')->latest('id')->paginate(10);

        return view('user.announcements.index', [
            'announcements' => $announcements,
        ]);
    }
}
