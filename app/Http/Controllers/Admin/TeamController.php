<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralEarning;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $referrers = User::query()
            ->where('role', 'user')
            ->where('salesperson_status', 'approved')
            ->withCount('teamMembers')
            ->having('team_members_count', '>', 0)
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            }))
            ->orderByDesc('team_members_count')
            ->paginate(20)
            ->withQueryString()
            ->through(function (User $user) {
                $user->total_earnings = $user->totalReferralEarnings();

                return $user;
            });

        return view('admin.teams.index', [
            'referrers' => $referrers,
        ]);
    }

    public function show(User $user): View
    {
        $user->load(['teamMembers' => fn ($query) => $query->orderByDesc('created_at')]);

        $earnings = $user->referralEarnings()->with(['referredUser', 'creator'])->latest()->get();

        return view('admin.teams.show', [
            'referrer' => $user,
            'earnings' => $earnings,
        ]);
    }

    public function storeEarning(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'referred_user_id' => ['required', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        abort_unless($user->teamMembers()->where('id', $data['referred_user_id'])->exists(), 422, 'That user is not on this salesperson\'s team.');

        ReferralEarning::create([
            'referrer_id' => $user->id,
            'referred_user_id' => $data['referred_user_id'],
            'amount' => $data['amount'],
            'note' => $data['note'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.teams.show', $user)->with('status', 'Earning logged.');
    }

    public function destroyEarning(ReferralEarning $earning): RedirectResponse
    {
        $referrerId = $earning->referrer_id;
        $earning->delete();

        return redirect()->route('admin.teams.show', $referrerId)->with('status', 'Earning entry removed.');
    }
}
