<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PointsController extends Controller
{
    /**
     * Lets client-side code (e.g. after answering a Resource video quiz) refresh
     * the topbar "pts" badge without a full page reload.
     */
    public function show(Request $request): JsonResponse
    {
        $points = $request->user()->combinedPoints();

        return response()->json([
            'earned' => $points->earned,
            'total' => $points->total,
        ]);
    }
}
