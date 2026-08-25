<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $user->load(['teamMembers' => fn ($query) => $query->orderByDesc('created_at')]);

        $earnings = $user->referralEarnings()->with('referredUser')->latest()->get();

        return view('user.team.index', [
            'user' => $user,
            'earnings' => $earnings,
            'totalEarnings' => $user->totalReferralEarnings(),
            'referralUrl' => route('register', ['ref' => $user->referral_code]),
        ]);
    }
}
