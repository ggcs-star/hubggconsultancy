<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));

        $events = Event::query()
            ->withCount('registrations')
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->when($status === 'published', fn ($query) => $query->where('is_published', true))
            ->when($status === 'draft', fn ($query) => $query->where('is_published', false))
            ->orderBy('starts_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.events.index', [
            'events' => $events,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateEvent($request);
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        Event::create($data);

        return redirect()->route('admin.events.index')->with('status', 'Event added.');
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $data = $this->validateEvent($request);
        $data['updated_by'] = auth()->id();

        $event->update($data);

        return redirect()->route('admin.events.index')->with('status', 'Event updated.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('admin.events.index')->with('status', 'Event deleted.');
    }

    public function togglePublish(Request $request, Event $event): RedirectResponse
    {
        $data = $request->validate(['is_published' => ['required', 'boolean']]);

        $event->update($data + ['updated_by' => auth()->id()]);

        return back()->with('status', $event->is_published ? "\"{$event->title}\" published." : "\"{$event->title}\" set to draft.");
    }

    public function registrants(Event $event): View
    {
        $registrants = $event->registrants()->orderBy('event_registrations.created_at', 'desc')->get();

        return view('admin.events.registrants', [
            'event' => $event,
            'registrants' => $registrants,
        ]);
    }

    private function validateEvent(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
