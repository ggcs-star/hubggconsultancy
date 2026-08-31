<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HallOfFameEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HallOfFameController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $pointsMin = $request->query('points_min');
        $pointsMax = $request->query('points_max');
        $periodFrom = $request->query('period_from');
        $periodTo = $request->query('period_to');

        $entries = HallOfFameEntry::visible()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->when(filled($pointsMin), fn ($query) => $query->where('points', '>=', $pointsMin))
            ->when(filled($pointsMax), fn ($query) => $query->where('points', '<=', $pointsMax))
            ->inPeriod($periodFrom, $periodTo)
            ->ordered()
            ->get();

        return view('user.hall-of-fame.index', [
            'entries' => $entries,
            'search' => $search,
            'pointsMin' => $pointsMin,
            'pointsMax' => $pointsMax,
            'periodFrom' => $periodFrom,
            'periodTo' => $periodTo,
        ]);
    }
}
