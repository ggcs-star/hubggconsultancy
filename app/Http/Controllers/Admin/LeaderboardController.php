<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contest;
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

        return view('admin.leaderboard.index', [
            'contests' => $contests,
            'selectedContest' => $selectedContest,
            'ranked' => $selectedContest ? $selectedContest->rankedParticipants() : collect(),
        ]);
    }
}
