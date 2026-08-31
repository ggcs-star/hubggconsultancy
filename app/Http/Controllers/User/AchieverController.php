<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Traits\ResolvesPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AchieverController extends Controller
{
    use ResolvesPeriod;

    public function index(Request $request): View
    {
        $period = in_array($request->string('period')->value(), ['today', 'week', 'month'], true)
            ? $request->string('period')->value()
            : 'all';

        [$from, $to] = $this->resolvePeriodRange($period);

        $certificates = Certificate::with(['user', 'course'])
            ->issuedBetween($from, $to)
            ->latest('issued_at')
            ->paginate(10)
            ->withQueryString();

        return view('user.achievers.index', [
            'certificates' => $certificates,
            'period' => $period,
        ]);
    }
}
