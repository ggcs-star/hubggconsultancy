<x-layout title="Ranking / Leaderboard" title-icon="star" subtitle="Top performers for a contest">

    @php
        $rankStyles = [
            1 => ['ring' => 'ring-amber-400', 'badge' => 'bg-amber-500'],
            2 => ['ring' => 'ring-indigo-300', 'badge' => 'bg-indigo-500'],
            3 => ['ring' => 'ring-orange-300', 'badge' => 'bg-orange-500'],
        ];
        $defaultRankStyle = ['ring' => 'ring-slate-200', 'badge' => 'bg-brand-600'];
        $podium = $ranked->take(3);
        $podiumHeights = [1 => 'h-16', 2 => 'h-11', 3 => 'h-9'];
        $podiumAvatarSize = [1 => 'h-14 w-14', 2 => 'h-10 w-10', 3 => 'h-10 w-10'];
        $topPerformer = $ranked->first();
    @endphp

    @if ($contests->isEmpty())
        <div class="card p-10 text-center text-sm text-slate-400">No contests to rank yet.</div>
    @else
        <div class="relative w-full overflow-hidden rounded-2xl border border-brand-100 bg-gradient-to-br from-brand-50 to-white p-5 sm:max-w-sm">
            <span class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-brand-600">
                <x-icon name="trophy" class="h-5 w-5" />
            </span>
            <form method="GET" class="relative pr-12">
                <label class="form-label">Contest</label>
                <select name="contest" class="form-input" onchange="this.form.submit()">
                    @foreach ($contests as $contest)
                        <option value="{{ $contest->id }}" @selected($selectedContest?->id === $contest->id)>{{ $contest->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @if ($selectedContest)
            <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="relative overflow-hidden card p-6 lg:col-span-2">
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-brand-50 via-white to-white"></div>
                    <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-brand-100/50"></div>
                    <div class="relative flex items-center gap-4">
                        <img src="{{ asset('images/award.jpg') }}" alt="" class="h-16 w-16 shrink-0 rounded-full object-cover">
                        <div>
                            <p class="text-lg font-bold text-slate-800">{{ $selectedContest->name }}</p>
                            <p class="mt-0.5 text-sm text-slate-500">Target: <span class="font-semibold text-brand-700">{{ $selectedContest->targetLabel() }}</span></p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <div class="card flex items-center gap-3 p-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <x-icon name="users" class="h-5 w-5" />
                        </span>
                        <div>
                            <p class="text-xs text-slate-400">Total Participants</p>
                            <p class="text-lg font-extrabold text-slate-800">{{ $ranked->count() }}</p>
                        </div>
                    </div>
                    <div class="card flex items-center gap-3 p-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <x-icon name="coin" class="h-5 w-5" />
                        </span>
                        <div>
                            <p class="text-xs text-slate-400">Total Amount Achieved</p>
                            <p class="text-lg font-extrabold text-slate-800">{{ $selectedContest->formatAmount($ranked->sum('achieved_amount')) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($ranked->isEmpty())
                <div class="mt-6 card p-10 text-center text-sm text-slate-400">No participants yet.</div>
            @else
                <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="card p-6 lg:col-span-1">
                        <h2 class="font-bold text-slate-800">Leaderboard Snapshot</h2>

                        <div class="mt-6 flex items-end justify-center gap-3">
                            @foreach ([2, 1, 3] as $rankPos)
                                @php $participant = $podium->firstWhere('rank', $rankPos); @endphp
                                @if ($participant)
                                    @php $style = $rankStyles[$participant->rank] ?? $defaultRankStyle; @endphp
                                    <div class="flex flex-col items-center">
                                        <div class="relative">
                                            @if ($participant->rank === 1)
                                                <x-icon name="crown" class="absolute -top-4 left-1/2 h-5 w-5 -translate-x-1/2 text-amber-400" />
                                            @endif
                                            @include('partials.user-avatar', ['user' => $participant, 'size' => $podiumAvatarSize[$participant->rank]])
                                        </div>
                                        <div class="mt-2 flex {{ $podiumHeights[$participant->rank] }} w-14 items-start justify-center rounded-t-lg pt-1.5 text-sm font-bold text-white {{ $style['badge'] }}">
                                            {{ $participant->rank }}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="mt-6 space-y-3 border-t border-slate-100 pt-4">
                            @if ($topPerformer)
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                                        <x-icon name="trophy" class="h-4 w-4" />
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-xs text-slate-400">Top Performer</p>
                                        <p class="truncate text-sm font-bold text-slate-800">{{ $topPerformer->name }}{{ $topPerformer->id === auth()->id() ? ' (You)' : '' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                        <x-icon name="star" class="h-4 w-4" />
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-xs text-slate-400">Achievement</p>
                                        <p class="truncate text-sm font-bold text-slate-800">{{ $topPerformer->progress_percent }}% of Target</p>
                                    </div>
                                </div>
                            @endif
                            <div class="flex items-center gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                                    <x-icon name="clock" class="h-4 w-4" />
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs text-slate-400">Last Updated</p>
                                    <p class="truncate text-sm font-bold text-slate-800">{{ $lastUpdated ? $lastUpdated->format('d M Y, h:i A') : '—' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card overflow-hidden lg:col-span-2">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <h2 class="font-bold text-slate-800">Leaderboard</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[560px] text-left text-sm">
                                <thead>
                                    <tr class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-400">
                                        <th class="px-5 py-3 font-semibold">Rank</th>
                                        <th class="px-5 py-3 font-semibold">Participant</th>
                                        <th class="px-5 py-3 font-semibold">Amount Achieved</th>
                                        <th class="px-5 py-3 font-semibold">Progress</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($ranked as $participant)
                                        @php $style = $rankStyles[$participant->rank] ?? $defaultRankStyle; @endphp
                                        <tr class="transition hover:bg-slate-50/60 {{ $participant->id === auth()->id() ? 'bg-brand-50/40' : '' }}">
                                            <td class="px-5 py-4">
                                                @include('partials.rank-medal', ['rank' => $participant->rank])
                                            </td>
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-3">
                                                    @include('partials.user-avatar', ['user' => $participant])
                                                    <div class="min-w-0">
                                                        <p class="truncate font-semibold text-slate-800">{{ $participant->name }}{{ $participant->id === auth()->id() ? ' (You)' : '' }}</p>
                                                        <p class="truncate text-xs text-slate-400">{{ $participant->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 font-semibold text-slate-700">
                                                {{ $selectedContest->formatAmount($participant->achieved_amount) }}
                                            </td>
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-9 shrink-0 text-xs font-semibold text-slate-500">{{ $participant->progress_percent }}%</span>
                                                    <div class="h-2 w-24 overflow-hidden rounded-full bg-slate-100">
                                                        <div class="h-full rounded-full {{ $style['badge'] }}" style="width: {{ min($participant->progress_percent, 100) }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endif

</x-layout>
