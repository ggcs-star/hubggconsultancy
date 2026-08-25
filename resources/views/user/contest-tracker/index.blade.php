@php
    $medals = ['🥇', '🥈', '🥉'];
@endphp

<x-layout title="Contest Tracker" title-icon="trending-up" subtitle="Live standings across every active contest">

    @forelse ($tracker as $row)
        @php
            $contest = $row['contest'];
            $ranked = $row['ranked'];
            $completed = $contest->hasEnded();
        @endphp

        <div class="card mb-6">
            <div class="border-b border-slate-100 px-5 py-4">
                <p class="font-bold text-slate-800">{{ $contest->name }}</p>
                <p class="mt-0.5 text-xs text-slate-400">
                    Target: {{ $contest->targetLabel() }}
                    &middot; {{ $contest->starts_at->format('d M') }} &ndash; {{ $contest->ends_at->format('d M Y') }}
                    @if ($completed)
                        &middot; <span class="font-semibold text-slate-500">Completed</span>
                    @endif
                </p>
            </div>

            @if ($ranked->isEmpty())
                <div class="px-6 py-10 text-center text-sm text-slate-400">No participants yet.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-400">
                                <th class="px-5 py-3 font-semibold">Rank</th>
                                <th class="px-5 py-3 font-semibold">Salesperson</th>
                                <th class="px-5 py-3 font-semibold">Target</th>
                                <th class="px-5 py-3 font-semibold">Achieved</th>
                                <th class="px-5 py-3 font-semibold">Progress</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($ranked as $participant)
                                <tr class="{{ $participant->id === auth()->id() ? 'bg-brand-50/40' : '' }}">
                                    <td class="px-5 py-3.5 font-semibold text-slate-700">{{ $medals[$participant->rank - 1] ?? $participant->rank }} {{ $participant->rank }}</td>
                                    <td class="px-5 py-3.5">
                                        <p class="font-medium text-slate-800">{{ $participant->name }}{{ $participant->id === auth()->id() ? ' (You)' : '' }}</p>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-600">{{ $contest->targetLabel() }}</td>
                                    <td class="px-5 py-3.5 font-semibold text-brand-700">{{ $contest->formatAmount($participant->achieved_amount) }}</td>
                                    <td class="px-5 py-3.5 text-slate-600">{{ $participant->progress_percent }}%</td>
                                    <td class="px-5 py-3.5">
                                        @if ($completed)
                                            <span class="badge badge-slate">{{ $participant->rank === 1 ? 'Winner' : 'Finished' }}</span>
                                        @else
                                            <span class="badge {{ $participant->rank === 1 ? 'badge-green' : 'badge-slate' }}">{{ $participant->rank === 1 ? 'Leading' : 'Active' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @empty
        <div class="card p-10 text-center text-sm text-slate-400">No active contests to track yet.</div>
    @endforelse

</x-layout>
