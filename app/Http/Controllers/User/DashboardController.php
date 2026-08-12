<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\OnboardingAssessmentScorer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, OnboardingAssessmentScorer $scorer): View
    {
        return view('user.dashboard', [
            'user' => $request->user(),
            'assessmentScore' => $scorer->score($request->user()),
        ]);
    }
}
