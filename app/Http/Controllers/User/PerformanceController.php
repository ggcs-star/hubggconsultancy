<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerformanceController extends Controller
{
    /** Points thresholds mirroring User::tierFor(), used to show progress toward the next tier. */
    private const TIER_THRESHOLDS = [
        'Bronze Performer' => 0,
        'Silver Performer' => 1000,
        'Gold Performer' => 3000,
        'Platinum Performer' => 7000,
    ];

    public function index(Request $request): View
    {
        $user = $request->user();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $points = $user->totalPoints();
        $tier = $user->tier();

        $recentCertificates = $user->certificates()
            ->with('course')
            ->latest('issued_at')
            ->take(5)
            ->get();

        return view('user.performance.index', [
            'points' => $points,
            'pointsThisMonth' => $user->pointsEarnedBetween($monthStart, $monthEnd),
            'tier' => $tier,
            'tierProgress' => $this->tierProgress($points, $tier),
            'certificatesTotal' => $user->certificatesCountBetween(),
            'certificatesThisMonth' => $user->certificatesCountBetween($monthStart, $monthEnd),
            'avgScore' => $user->averageCourseScorePercent(),
            'leadsWonTotal' => $user->leadsWonCountBetween(),
            'leadsWonThisMonth' => $user->leadsWonCountBetween($monthStart, $monthEnd),
            'activeDaysThisMonth' => $user->activeLearningDaysBetween($monthStart, $monthEnd),
            'recentCertificates' => $recentCertificates,
        ]);
    }

    private function tierProgress(int $points, string $tier): array
    {
        $tiers = self::TIER_THRESHOLDS;
        $tierNames = array_keys($tiers);
        $currentIndex = array_search($tier, $tierNames, true);
        $nextTier = $tierNames[$currentIndex + 1] ?? null;

        if ($nextTier === null) {
            return ['nextTier' => null, 'percent' => 100, 'pointsToNext' => 0];
        }

        $floor = $tiers[$tier];
        $ceiling = $tiers[$nextTier];
        $percent = (int) round((($points - $floor) / ($ceiling - $floor)) * 100);

        return [
            'nextTier' => $nextTier,
            'percent' => max(0, min(100, $percent)),
            'pointsToNext' => max(0, $ceiling - $points),
        ];
    }
}
