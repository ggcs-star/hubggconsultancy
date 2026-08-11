<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalespersonApplicationController extends Controller
{
    public function show(Request $request): View
    {
        return view('user.apply-salesperson', ['user' => $request->user()]);
    }

    public function apply(Request $request): RedirectResponse
    {
        $request->user()->update(['salesperson_status' => 'pending']);

        return back()->with('status', 'Your application has been submitted for review.');
    }
}
