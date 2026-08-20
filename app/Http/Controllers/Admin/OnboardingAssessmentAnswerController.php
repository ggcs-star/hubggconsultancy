<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingAssessmentAnswer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OnboardingAssessmentAnswerController extends Controller
{
    public function grade(Request $request, OnboardingAssessmentAnswer $answer): RedirectResponse
    {
        $questionPoints = $answer->question_points ?? $answer->question->points;

        $data = $request->validate([
            'points_awarded' => ['required', 'integer', 'min:0', 'max:' . $questionPoints],
        ]);

        $answer->update([
            'points_awarded' => $data['points_awarded'],
            'is_correct' => $data['points_awarded'] >= $questionPoints,
            'graded_by' => $request->user()->id,
            'graded_at' => now(),
        ]);

        return back()->with('status', 'Grade saved.');
    }
}
