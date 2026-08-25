<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use Illuminate\View\View;

class ContestTrackerController extends Controller
{
    public function index(): View
    {
        $contests = Contest::active()->withCount('registrations')->orderByDesc('starts_at')->get();

        $contests->each(fn (Contest $contest) => $contest->finalizeIfEnded());

        $tracker = $contests->map(fn (Contest $contest) => [
            'contest' => $contest,
            'ranked' => $contest->rankedParticipants(),
        ]);

        return view('admin.contest-tracker.index', [
            'tracker' => $tracker,
        ]);
    }
}
