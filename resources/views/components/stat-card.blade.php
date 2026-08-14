@props(['icon', 'color' => 'primary', 'value', 'label', 'description' => null, 'trend' => null])

@php
    $colorMap = [
        'primary' => ['solid' => 'bg-primary', 'stroke' => '#7c3aed', 'muted' => 'rgba(124, 58, 237, 0.3)'],
        'success' => ['solid' => 'bg-success', 'stroke' => '#059669', 'muted' => 'rgba(5, 150, 105, 0.3)'],
        'warning' => ['solid' => 'bg-warning', 'stroke' => '#d97706', 'muted' => 'rgba(217, 119, 6, 0.3)'],
        'danger' => ['solid' => 'bg-danger', 'stroke' => '#dc2626', 'muted' => 'rgba(220, 38, 38, 0.3)'],
        'chart-4' => ['solid' => 'bg-chart-4', 'stroke' => '#0ea5e9', 'muted' => 'rgba(14, 165, 233, 0.3)'],
        'secondary' => ['solid' => 'bg-secondary', 'stroke' => '#475569', 'muted' => 'rgba(71, 85, 105, 0.3)'],
        'brand' => ['solid' => 'bg-brand-700', 'stroke' => '#6d28d9', 'muted' => 'rgba(109, 40, 217, 0.3)'],
    ];
    $palette = $colorMap[$color] ?? $colorMap['primary'];

    $points = null;
    $lastSegment = null;
    if (is_array($trend) && count($trend) >= 2) {
        $width = 96;
        $height = 32;
        $min = min($trend);
        $max = max($trend);
        $range = $max - $min ?: 1;
        $count = count($trend);
        $coords = [];
        foreach (array_values($trend) as $i => $v) {
            $x = $count > 1 ? ($i / ($count - 1)) * $width : 0;
            $y = $height - (($v - $min) / $range) * ($height - 6) - 3;
            $coords[] = [round($x, 1), round($y, 1)];
        }
        $points = implode(' ', array_map(fn ($c) => "{$c[0]},{$c[1]}", $coords));
        $last = array_slice($coords, -2);
        $lastSegment = implode(' ', array_map(fn ($c) => "{$c[0]},{$c[1]}", $last));
    }
@endphp

<div class="card relative overflow-hidden p-5">
    <div class="flex items-center gap-3">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ $palette['solid'] }} text-white">
            <x-icon :name="$icon" class="h-5 w-5" />
        </span>
        <span class="text-sm font-semibold text-slate-500">{{ $label }}</span>
    </div>

    <p class="mt-3 text-2xl font-extrabold text-slate-800">{{ $value }}</p>

    @if ($description)
        <p class="mt-0.5 text-xs text-slate-400">{{ $description }}</p>
    @endif

    @if ($points)
        <svg viewBox="0 0 96 32" width="96" height="32" class="pointer-events-none absolute bottom-3 right-3" aria-hidden="true">
            <polyline points="{{ $points }}" fill="none" stroke="{{ $palette['muted'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <polyline points="{{ $lastSegment }}" fill="none" stroke="{{ $palette['stroke'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    @endif
</div>
