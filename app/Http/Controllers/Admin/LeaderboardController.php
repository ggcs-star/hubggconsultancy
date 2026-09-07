<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\RankMedal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(Request $request): View
    {
        $contests = Contest::active()->orderByDesc('starts_at')->get();

        $contests->each(fn (Contest $contest) => $contest->finalizeIfEnded());

        $selectedContest = $contests->firstWhere('id', (int) $request->query('contest'))
            ?? $contests->first(fn (Contest $contest) => $contest->displayStatus() === 'active')
            ?? $contests->first();

        $lastUpdated = $selectedContest ? $selectedContest->achievements()->max('created_at') : null;

        return view('admin.leaderboard.index', [
            'contests' => $contests,
            'selectedContest' => $selectedContest,
            'ranked' => $selectedContest ? $selectedContest->rankedParticipants() : collect(),
            'lastUpdated' => $lastUpdated ? \Illuminate\Support\Carbon::parse($lastUpdated) : null,
            'rankMedals' => RankMedal::orderBy('rank')->get(),
        ]);
    }

    public function updateMedals(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'medals' => ['required', 'array'],
            'medals.*' => ['required', 'string', 'max:10'],
        ]);

        // Updated one at a time (not a single query-builder update()) so the
        // model's saved() event fires and busts the cached rank => emoji map
        // — only ever a handful of rows, so the extra queries are trivial.
        foreach ($data['medals'] as $rank => $emoji) {
            RankMedal::where('rank', $rank)->first()?->update(['emoji' => $emoji]);
        }

        return back()->with('status', 'Rank medals updated.');
    }
}
