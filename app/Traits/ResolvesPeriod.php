<?php

namespace App\Traits;

trait ResolvesPeriod
{
    /**
     * [$from, $to] Carbon bounds for a period key, or [null, null] for
     * 'all'/unrecognized keys so callers can skip the where clause entirely.
     */
    protected function resolvePeriodRange(string $period): array
    {
        return match ($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
            default => [null, null],
        };
    }
}
