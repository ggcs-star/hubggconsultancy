@php
    $rankMedals = ['🥇', '🥈', '🥉'];
@endphp

<div class="flex items-center gap-1.5">
    @if (isset($rankMedals[$rank - 1]))
        <span class="text-xl leading-none">{{ $rankMedals[$rank - 1] }}</span>
        <span class="font-bold text-slate-700">{{ $rank }}</span>
    @else
        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
            {{ $rank }}
        </span>
    @endif
</div>
