<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OnboardingAssessmentScorer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnboardingAssessmentResultController extends Controller
{
    public function show(Request $request, OnboardingAssessmentScorer $scorer): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string'],
        ]);

        $digits = preg_replace('/\D+/', '', $validated['phone']);
        $last10 = substr($digits, -10);

        $user = User::whereRaw(
            "REPLACE(REPLACE(REPLACE(phone, '+', ''), ' ', ''), '-', '') LIKE ?",
            ["%{$last10}"]
        )->first();

        if (! $user) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'No user found with this phone number.',
            ], 404);
        }

        $score = $scorer->score($user);

        if (! $score->attempted) {
            return response()->json([
                'status' => 'not_attempted',
                'message' => 'This user has not attempted the onboarding assessment yet.',
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'points' => $score->earned_points.'/'.$score->total_points,
            'score_percent' => $score->percent,
            'assessment_status' => $score->status,
        ]);
    }
}
