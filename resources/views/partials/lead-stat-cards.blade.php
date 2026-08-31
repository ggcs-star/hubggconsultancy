@php
    $leadStatCards = [
        ['key' => 'new', 'label' => 'New Leads', 'icon' => 'users', 'iconBox' => 'bg-violet-100 text-violet-600'],
        ['key' => 'follow_ups', 'label' => 'Follow-ups Due', 'icon' => 'phone', 'iconBox' => 'bg-orange-100 text-orange-600'],
        ['key' => 'qualified', 'label' => 'Qualified', 'icon' => 'check-circle', 'iconBox' => 'bg-blue-100 text-blue-600'],
        ['key' => 'opportunities', 'label' => 'Opportunities', 'icon' => 'gift', 'iconBox' => 'bg-pink-100 text-pink-600'],
        ['key' => 'won', 'label' => 'Won', 'icon' => 'trophy', 'iconBox' => 'bg-gradient-to-br from-emerald-400 to-teal-500 text-white'],
    ];
@endphp

<div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-5">
    @foreach ($leadStatCards as $card)
        @php $stat = $stats[$card['key']]; @endphp
        <div class="card p-4">
            <div class="flex items-start justify-between gap-2">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $card['iconBox'] }}">
                    <x-icon name="{{ $card['icon'] }}" class="h-5 w-5" />
                </span>
                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $stat['trend'] > 0 ? 'bg-emerald-50 text-emerald-600' : ($stat['trend'] < 0 ? 'bg-red-50 text-red-600' : 'bg-slate-100 text-slate-500') }}">
                    {{ $stat['trend'] > 0 ? '+' : '' }}{{ $stat['trend'] }}%
                </span>
            </div>
            <p class="mt-3 text-xl font-extrabold text-slate-800 sm:text-2xl">{{ $stat['value'] }}</p>
            <p class="mt-0.5 text-xs text-slate-400">{{ $card['label'] }}</p>
            <p class="mt-1 text-[11px] text-slate-300">vs last 7 days</p>
        </div>
    @endforeach
</div>
