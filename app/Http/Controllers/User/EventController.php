<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = trim((string) $request->query('search'));

        $upcoming = Event::published()->with('registrations')->upcoming()->get();

        $past = Event::published()->with('registrations')->past()
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->paginate(10)
            ->withQueryString();

        return view('user.events.index', [
            'upcoming' => $upcoming,
            'past' => $past,
            'user' => $user,
        ]);
    }

    public function register(Request $request, Event $event): RedirectResponse
    {
        abort_unless($event->is_published, 404);

        $event->registrants()->syncWithoutDetaching([$request->user()->id]);

        return back()->with('status', "You're registered for \"{$event->title}\".");
    }

    public function unregister(Request $request, Event $event): RedirectResponse
    {
        $event->registrants()->detach($request->user()->id);

        return back()->with('status', "Registration for \"{$event->title}\" cancelled.");
    }
}
