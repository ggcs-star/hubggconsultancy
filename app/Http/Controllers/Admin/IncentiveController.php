<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncentiveEntry;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncentiveController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $baseQuery = IncentiveEntry::query()
            ->when($search !== '', fn ($query) => $query->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            }));

        $totalAmount = (clone $baseQuery)->sum('amount');

        $entries = $baseQuery
            ->with(['user', 'contest'])
            ->latest('awarded_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.incentives.index', [
            'entries' => $entries,
            'totalAmount' => $totalAmount,
            'users' => User::where('role', 'user')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'in:points,bonus,cash,gift'],
            'awarded_at' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        IncentiveEntry::create($data + [
            'source' => 'manual',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.incentives.index')->with('status', 'Incentive added.');
    }

    public function destroy(IncentiveEntry $incentive): RedirectResponse
    {
        $incentive->delete();

        return redirect()->route('admin.incentives.index')->with('status', 'Incentive entry removed.');
    }
}
