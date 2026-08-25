<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncentiveController extends Controller
{
    public function index(Request $request): View
    {
        $entries = $request->user()->incentiveEntries()->with('contest')->latest('awarded_at')->latest('id')->get();

        return view('user.incentives.index', [
            'entries' => $entries,
            'totalAmount' => $entries->sum('amount'),
        ]);
    }
}
