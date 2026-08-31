<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContestController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $upcoming = Contest::active()->with(['registrations', 'targetType'])->upcoming()->get();
        $past = Contest::active()->with(['registrations', 'targetType'])->past()->paginate(10)->withQueryString();

        $upcoming->each(fn (Contest $contest) => $contest->finalizeIfEnded());
        $past->each(fn (Contest $contest) => $contest->finalizeIfEnded());

        $targetTypes = $upcoming->merge($past->items())
            ->pluck('targetType')
            ->filter()
            ->unique('id')
            ->sortBy('sort_order')
            ->values();

        return view('user.contests.index', [
            'upcoming' => $upcoming,
            'past' => $past,
            'targetTypes' => $targetTypes,
            'user' => $user,
        ]);
    }

    public function register(Request $request, Contest $contest): RedirectResponse
    {
        abort_unless($contest->is_active, 404);

        if ($contest->hasEnded()) {
            return back()->with('status', 'This contest has ended.');
        }

        if ($contest->participant_mode !== 'open') {
            return back()->with('status', 'This contest is invite-only — an admin needs to add you as a participant.');
        }

        if ($request->user()->salesperson_status !== 'approved') {
            return back()->with('status', 'Only approved salespersons can join contests. Complete your onboarding to apply.');
        }

        $contest->participants()->syncWithoutDetaching([$request->user()->id]);

        return back()->with('status', "You're registered for \"{$contest->name}\".");
    }

    public function unregister(Request $request, Contest $contest): RedirectResponse
    {
        $contest->participants()->detach($request->user()->id);

        return back()->with('status', "Registration for \"{$contest->name}\" cancelled.");
    }
}
