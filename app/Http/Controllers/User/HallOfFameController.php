<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\HallOfFameService;
use App\Traits\ResolvesPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HallOfFameController extends Controller
{
    use ResolvesPeriod;

    public function __construct(private HallOfFameService $hallOfFameService)
    {
    }

    public function index(Request $request): View
    {
        $period = in_array($request->string('period')->value(), ['month', 'quarter'], true)
            ? $request->string('period')->value()
            : 'all';

        [$from, $to] = $this->resolvePeriodRange($period);

        $ranked = $this->hallOfFameService->rankedUsers($from, $to);

        return view('user.hall-of-fame.index', [
            'period' => $period,
            'podium' => $ranked->filter(fn ($row) => $row->points > 0)->sortByDesc('points')->values()->take(3),
            'topSales' => $ranked->filter(fn ($row) => $row->leadsWon > 0)->sortByDesc('leadsWon')->first(),
            'topLearning' => $ranked->filter(fn ($row) => $row->learningScore !== null)->sortByDesc('learningScore')->first(),
            'topCertifications' => $ranked->filter(fn ($row) => $row->certificates > 0)->sortByDesc('certificates')->first(),
            'topConsistent' => $ranked->filter(fn ($row) => $row->activeDays > 0)->sortByDesc('activeDays')->first(),
        ]);
    }
}
