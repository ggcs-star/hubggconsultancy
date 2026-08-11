<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SalespersonApplicationController extends Controller
{
    public function index(): View
    {
        $applications = User::where('role', 'user')
            ->where('salesperson_status', '!=', 'none')
            ->latest()
            ->paginate(10);

        return view('admin.salesperson-applications', compact('applications'));
    }

    public function approve(User $user): RedirectResponse
    {
        $user->update(['salesperson_status' => 'approved']);

        return back()->with('status', "{$user->name}'s application was approved.");
    }

    public function reject(User $user): RedirectResponse
    {
        $user->update(['salesperson_status' => 'rejected']);

        return back()->with('status', "{$user->name}'s application was rejected.");
    }
}
