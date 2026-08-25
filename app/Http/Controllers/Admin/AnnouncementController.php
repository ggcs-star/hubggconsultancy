<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::latest('published_at')->latest('id')->get();

        return view('admin.announcements.index', [
            'announcements' => $announcements,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateAnnouncement($request);
        $data['created_by'] = auth()->id();

        Announcement::create($data);

        return redirect()->route('admin.announcements.index')->with('status', 'Announcement posted.');
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $announcement->update($this->validateAnnouncement($request));

        return redirect()->route('admin.announcements.index')->with('status', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('status', 'Announcement deleted.');
    }

    public function toggleActive(Request $request, Announcement $announcement): RedirectResponse
    {
        $data = $request->validate(['is_active' => ['required', 'boolean']]);

        $announcement->update($data);

        return back()->with('status', $announcement->is_active ? 'Announcement activated.' : 'Announcement hidden.');
    }

    private function validateAnnouncement(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'published_at' => ['required', 'date'],
        ]);
    }
}
