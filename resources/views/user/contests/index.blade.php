<x-layout title="Contests" title-icon="gift" subtitle="Join active sales contests and track your progress">

    @php
        $isApproved = $user->salesperson_status === 'approved';
    @endphp

    @unless ($isApproved)
        <div class="card mb-6 flex items-start gap-3 border-l-4 border-l-amber-400 p-4">
            <x-icon name="lock" class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" />
            <div>
                <p class="text-sm font-semibold text-slate-700">Approved salespersons only</p>
                <p class="mt-0.5 text-sm text-slate-500">Complete your onboarding and get approved as a salesperson to join contests.</p>
            </div>
        </div>
    @endunless

    <div x-data="{ tab: 'all', upcomingTypeIds: @js($upcoming->pluck('target_type_id')) }">

    @if ($targetTypes->isNotEmpty())
        <div class="mb-5 flex flex-wrap gap-2">
            <button type="button" x-on:click="tab = 'all'" class="rounded-full px-4 py-2 text-sm font-semibold transition" :class="tab === 'all' ? 'bg-brand-600 text-white' : 'bg-white text-slate-500 hover:bg-slate-50'">
                All
            </button>
            @foreach ($targetTypes as $targetType)
                <button type="button" x-on:click="tab = '{{ $targetType->id }}'" class="rounded-full px-4 py-2 text-sm font-semibold transition" :class="tab === '{{ $targetType->id }}' ? 'bg-brand-600 text-white' : 'bg-white text-slate-500 hover:bg-slate-50'">
                    {{ $targetType->name }}
                </button>
            @endforeach
        </div>
    @endif

    <div class="space-y-5">
        @forelse ($upcoming as $contest)
            @php
                $registered = $contest->isRegisteredBy($user);
                $achieved = $registered ? $contest->totalAchievementFor($user) : 0;
                $progress = $registered ? $contest->progressPercentFor($user) : 0;
                $remaining = $registered ? $contest->remainingFor($user) : (float) $contest->target_value;
            @endphp

            <div class="card p-6" x-show="tab === 'all' || tab === '{{ $contest->target_type_id }}'" x-cloak>
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-2xl">🏆</span>
                        <div>
                            <p class="text-lg font-bold text-slate-800">{{ $contest->name }}</p>
                            @if ($contest->targetType)
                                <span class="badge badge-slate mt-1">{{ $contest->targetType->name }}</span>
                            @endif
                            @if ($contest->description)
                                <p class="mt-0.5 text-sm text-slate-500">{{ $contest->description }}</p>
                            @endif
                            <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-400">
                                <span class="flex items-center gap-1">
                                    <x-icon name="calendar" class="h-3.5 w-3.5" />
                                    {{ $contest->starts_at->format('d M') }} &ndash; {{ $contest->ends_at->format('d M Y') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <x-icon name="clock" class="h-3.5 w-3.5" />
                                    {{ $contest->daysRemaining() }} {{ Str::plural('Day', $contest->daysRemaining()) }} Remaining
                                </span>
                                <span class="flex items-center gap-1">
                                    <x-icon name="users" class="h-3.5 w-3.5" />
                                    {{ $contest->registrations->count() }} {{ Str::plural('person', $contest->registrations->count()) }} joined
                                </span>
                            </p>
                        </div>
                    </div>

                    @if ($registered)
                        <form
                            method="POST"
                            action="{{ route('user.contests.unregister', $contest) }}"
                            class="group shrink-0"
                            x-data=""
                            x-on:submit.prevent="$dispatch('confirm-action', { message: 'Leave \'{{ $contest->name }}\'? Your progress will no longer count.', target: $el })"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 transition group-hover:border-red-200 group-hover:bg-red-50 group-hover:text-red-600">
                                <x-icon name="check-circle" class="h-4 w-4 group-hover:hidden" />
                                <x-icon name="x" class="hidden h-4 w-4 group-hover:block" />
                                <span class="group-hover:hidden">Joined</span>
                                <span class="hidden group-hover:inline">Leave Contest</span>
                            </button>
                        </form>
                    @elseif ($contest->participant_mode !== 'open')
                        <span class="shrink-0 rounded-xl border border-slate-200 bg-slate-50 px-5 py-2 text-sm font-semibold text-slate-400">Invite Only</span>
                    @elseif ($isApproved)
                        <form method="POST" action="{{ route('user.contests.register', $contest) }}" class="shrink-0">
                            @csrf
                            <button type="submit" class="rounded-xl border border-brand-200 bg-white px-5 py-2 text-sm font-semibold text-brand-700 transition hover:bg-brand-50">Join Contest</button>
                        </form>
                    @else
                        <span class="shrink-0 rounded-xl border border-slate-200 bg-slate-50 px-5 py-2 text-sm font-semibold text-slate-400">Approval Required</span>
                    @endif
                </div>

                <div class="mt-5 grid grid-cols-2 gap-4 border-t border-slate-100 pt-5 sm:grid-cols-4">
                    <div>
                        <p class="text-xs text-slate-400">Target</p>
                        <p class="mt-0.5 font-bold text-slate-800">{{ $contest->targetLabel() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Current Achievement</p>
                        <p class="mt-0.5 font-bold text-slate-800">{{ $contest->formatAmount($achieved) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Progress</p>
                        <p class="mt-0.5 font-bold text-brand-700">{{ $progress }}% Achieved</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Reward</p>
                        <p class="mt-0.5 font-bold text-emerald-600">{{ $contest->reward ?: '—' }}</p>
                    </div>
                </div>

                @if ($registered)
                    <div class="mt-4">
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-brand-600" style="width: {{ min($progress, 100) }}%"></div>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-400">{{ $contest->formatAmount($remaining) }} remaining to reach the target</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="card p-10 text-center text-sm text-slate-400">No active contests right now — check back soon.</div>
        @endforelse

        @if ($upcoming->isNotEmpty())
            <div class="card p-10 text-center text-sm text-slate-400" x-show="tab !== 'all' && !upcomingTypeIds.includes(Number(tab))" x-cloak>
                No upcoming contests in this category.
            </div>
        @endif
    </div>

    @if ($past->isNotEmpty())
        <div class="card mt-6">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="font-bold text-slate-800">Past Contests</h2>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach ($past as $contest)
                    @php $achieved = $contest->isRegisteredBy($user) ? $contest->totalAchievementFor($user) : 0; @endphp
                    <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 opacity-70" x-show="tab === 'all' || tab === '{{ $contest->target_type_id }}'" x-cloak>
                        <div class="flex items-center gap-4">
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-2xl">🏆</span>
                            <div>
                                <p class="font-bold text-slate-700">{{ $contest->name }}</p>
                                <p class="mt-0.5 flex items-center gap-3 text-xs text-slate-400">
                                    @if ($contest->targetType)
                                        <span>{{ $contest->targetType->name }}</span>
                                    @endif
                                    <span>{{ $contest->registrations->count() }} {{ Str::plural('person', $contest->registrations->count()) }} joined</span>
                                    @if ($contest->isRegisteredBy($user))
                                        <span>Your achievement: {{ $contest->formatAmount($achieved) }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <span class="badge badge-slate shrink-0">Ended</span>
                    </div>
                @endforeach
            </div>

            @if ($past->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $past->links() }}
                </div>
            @endif
        </div>
    @endif

    </div>

</x-layout>
