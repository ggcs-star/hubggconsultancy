@php
    $status = $contest->displayStatus();
    $statusBadge = [
        'draft' => ['label' => 'Draft', 'class' => 'badge-slate'],
        'active' => ['label' => 'Active', 'class' => 'badge-green'],
        'completed' => ['label' => 'Completed', 'class' => 'badge-slate'],
    ];
    $medals = ['🥇', '🥈', '🥉'];
@endphp

<x-layout title="Contest Participants">

    <a href="{{ route('admin.contests.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
        <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
        Back to Contests
    </a>

    <div class="card mt-4 p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-brand-50 text-2xl">🏆</span>
                <div>
                    <div class="flex items-center gap-2">
                        <p class="font-bold text-slate-800">{{ $contest->name }}</p>
                        <span class="badge {{ $statusBadge[$status]['class'] }}">{{ $statusBadge[$status]['label'] }}</span>
                    </div>
                    <p class="mt-1 text-sm text-slate-400">
                        {{ $contest->starts_at->format('d M Y') }} &ndash; {{ $contest->ends_at->format('d M Y') }}
                        &middot; Target: {{ $contest->targetLabel() }}
                        @if ($contest->reward)
                            &middot; Reward: {{ $contest->reward }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="text-right">
                <p class="text-2xl font-extrabold text-brand-700">{{ $participants->count() }}</p>
                <p class="text-xs text-slate-400">{{ Str::plural('Participant', $participants->count()) }}</p>
            </div>
        </div>
    </div>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Live Ranking</h2>
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
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($ranked as $participant)
                            <tr>
                                <td class="px-5 py-3.5 font-semibold text-slate-700">{{ $medals[$participant->rank - 1] ?? $participant->rank }}</td>
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-slate-800">{{ $participant->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $participant->email }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">{{ $contest->targetLabel() }}</td>
                                <td class="px-5 py-3.5 font-semibold text-brand-700">{{ $contest->formatAmount($participant->achieved_amount) }}</td>
                                <td class="px-5 py-3.5 text-slate-600">{{ $participant->progress_percent }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="card">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="font-bold text-slate-800">Participants</h2>
                <span class="badge badge-slate">{{ $participants->count() }}</span>
            </div>

            @if ($participants->isEmpty())
                <div class="px-6 py-10 text-center text-sm text-slate-400">
                    No participants yet.
                    @if ($contest->participant_mode === 'open')
                        Once approved salespersons join, they'll show up here.
                    @else
                        Edit this contest to select participants.
                    @endif
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($participants as $participant)
                        <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-500">
                                    {{ strtoupper(substr($participant->name, 0, 1)) }}
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $participant->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $participant->email }}</p>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400">Joined {{ $participant->pivot->created_at->format('d M Y') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="font-bold text-slate-800">Log an Achievement</h2>
            </div>

            @if ($contest->isCrmDriven())
                <div class="px-6 py-10 text-center text-sm text-slate-400">
                    This contest awards points automatically from CRM lead activity — manual logging is disabled.
                    <a href="{{ route('admin.contests.edit', $contest) }}" class="font-semibold text-brand-700 hover:underline">Edit point rules</a>
                </div>
            @elseif ($contest->hasEnded())
                <div class="px-6 py-10 text-center text-sm text-slate-400">This contest has ended — no more achievements can be logged.</div>
            @elseif ($participants->isEmpty())
                <div class="px-6 py-10 text-center text-sm text-slate-400">Add participants first before logging achievements.</div>
            @else
                <form method="POST" action="{{ route('admin.contests.achievements.store', $contest) }}" class="space-y-4 p-5">
                    @csrf

                    <div>
                        <label class="form-label">Participant</label>
                        <select name="user_id" required class="form-input">
                            <option value="">Select a participant</option>
                            @foreach ($participants as $participant)
                                <option value="{{ $participant->id }}" @selected(old('user_id') == $participant->id)>{{ $participant->name }} ({{ $participant->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Amount (₹)</label>
                        <input type="number" name="amount" step="0.01" min="0.01" required placeholder="500000" value="{{ old('amount') }}" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Note</label>
                        <input type="text" name="note" value="{{ old('note') }}" placeholder="e.g. Sale 1" class="form-input">
                    </div>

                    <button type="submit" class="btn-primary w-full">Log Achievement</button>
                </form>
            @endif
        </div>
    </div>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Achievement History</h2>
        </div>

        @if ($achievements->isEmpty())
            <div class="px-6 py-10 text-center text-sm text-slate-400">No achievements logged yet.</div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($achievements as $achievement)
                    <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800">
                                {{ $contest->formatAmount($achievement->amount) }}
                                <span class="font-normal text-slate-400">— {{ $achievement->user->name }}</span>
                                @if ($achievement->lead_id)
                                    <span class="badge badge-slate">Auto (CRM)</span>
                                @endif
                            </p>
                            @if ($achievement->note)
                                <p class="mt-0.5 truncate text-xs text-slate-400">{{ $achievement->note }}</p>
                            @endif
                            <p class="mt-0.5 text-xs text-slate-400">
                                Logged {{ $achievement->created_at->format('d M Y, h:i A') }}
                                @if ($achievement->creator)
                                    by {{ $achievement->creator->name }}
                                @endif
                            </p>
                        </div>
                        <form method="POST" action="{{ route('admin.contest-achievements.destroy', $achievement) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Remove this {{ $contest->formatAmount($achievement->amount) }} entry?', target: $el })">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</x-layout>
