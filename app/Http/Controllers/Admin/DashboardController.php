<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'pending_applications' => User::where('salesperson_status', 'pending')->count(),
            'approved_salespeople' => User::where('salesperson_status', 'approved')->count(),
            'profile_completed' => User::where('role', 'user')->where('profile_completed', true)->count(),
        ];

        $recentUsers = User::where('role', 'user')->latest()->take(6)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }
}
