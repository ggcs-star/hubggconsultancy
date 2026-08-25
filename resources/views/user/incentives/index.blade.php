@php
    $typeBadge = [
        'points' => 'badge-slate',
        'bonus' => 'badge-green',
        'cash' => 'badge-green',
        'gift' => 'badge-amber',
    ];
@endphp

<x-layout title="Incentives & Earnings" title-icon="coin" subtitle="Rewards and bonuses you've earned">

    <div class="card p-6">
        <p class="text-3xl font-extrabold text-brand-700">₹{{ number_format($totalAmount, 2) }}</p>
        <p class="mt-1 text-sm text-slate-400">Total Earnings</p>
    </div>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Earnings History</h2>
        </div>

        @if ($entries->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                    <x-icon name="coin" class="h-7 w-7 text-brand-600" />
                </div>
                <h3 class="mt-4 font-bold text-slate-800">No earnings yet</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">Win a contest or receive an admin-granted bonus and it'll show up here.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($entries as $entry)
                    <div class="flex items-center justify-between gap-3 px-5 py-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-xl">
                                {{ $entry->source === 'contest' ? '🏆' : '🎁' }}
                            </span>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $entry->title }}</p>
                                @if ($entry->note)
                                    <p class="text-xs text-slate-400">{{ $entry->note }}</p>
                                @endif
                                <p class="mt-0.5 text-xs text-slate-400">{{ $entry->awarded_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-emerald-600">+₹{{ number_format($entry->amount, 2) }}</p>
                            <span class="badge {{ $typeBadge[$entry->type] ?? 'badge-slate' }} mt-1">{{ $entry->typeLabel() }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</x-layout>
