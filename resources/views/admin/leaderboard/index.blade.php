@php
    $medals = ['🥇', '🥈', '🥉'];
@endphp

<x-layout title="Ranking / Leaderboard" title-icon="star" subtitle="Top performers for a contest">

    @if ($contests->isEmpty())
        <div class="card p-10 text-center text-sm text-slate-400">No contests to rank yet.</div>
    @else
        <form method="GET" class="w-full sm:max-w-sm">
            <label class="form-label">Contest</label>
            <select name="contest" class="form-input" onchange="this.form.submit()">
                @foreach ($contests as $contest)
                    <option value="{{ $contest->id }}" @selected($selectedContest?->id === $contest->id)>{{ $contest->name }}</option>
                @endforeach
            </select>
        </form>

        @if ($selectedContest)
            <div class="mt-6 card p-8">
                <p class="text-center text-lg font-bold text-slate-800">{{ $selectedContest->name }}</p>
                <p class="mt-1 text-center text-sm text-slate-400">Target: {{ $selectedContest->targetLabel() }}</p>

                @if ($ranked->isEmpty())
                    <div class="mt-8 text-center text-sm text-slate-400">No participants yet.</div>
                @else
                    <div class="mt-8 space-y-3">
                        @foreach ($ranked->take(10) as $participant)
                            <div class="flex items-center justify-between gap-4 rounded-xl border border-slate-100 px-5 py-4 {{ $participant->rank <= 3 ? 'bg-brand-50/40' : '' }}">
                                <div class="flex items-center gap-4">
                                    <span class="text-2xl">{{ $medals[$participant->rank - 1] ?? $participant->rank . '.' }}</span>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $participant->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $participant->email }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-extrabold text-brand-700">{{ $selectedContest->formatAmount($participant->achieved_amount) }}</p>
                                    <p class="text-xs text-slate-400">{{ $participant->progress_percent }}%</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    @endif

</x-layout>
