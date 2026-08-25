@php
    $ringRadius = 45;
    $ringCircumference = 2 * M_PI * $ringRadius;
    $trainingOffset = $ringCircumference * (1 - min(max($trainingProgress->percent, 0), 100) / 100);
    $contestOffset = $contestProgress ? $ringCircumference * (1 - min(max($contestProgress->percent, 0), 100) / 100) : $ringCircumference;
@endphp

<x-layout title="Dashboard" subtitle="Welcome back, {{ explode(' ', $user->name)[0] }}!">

    {{-- Top stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-700 text-white">
                    <x-icon name="star" class="h-5 w-5" />
                </span>
                <div class="min-w-0">
                    <p class="text-xs text-slate-400">My Rank</p>
                    <p class="text-xl font-extrabold text-slate-800">{{ $rank->position ?? '—' }}</p>
                </div>
            </div>
            <p class="mt-2 text-xs text-slate-400">{{ $rank->tier }}</p>
            <a href="{{ route('user.leaderboard.index') }}" class="mt-3 flex items-center gap-1 text-xs font-semibold text-brand-700 hover:text-brand-800">
                View Leaderboard <x-icon name="chevron-right" class="h-3 w-3" />
            </a>
        </div>

        <div class="card p-5">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-500 text-white">
                    <x-icon name="book-open" class="h-5 w-5" />
                </span>
                <div class="min-w-0">
                    <p class="text-xs text-slate-400">Training Progress</p>
                    <p class="text-xl font-extrabold text-slate-800">{{ $trainingProgress->percent }}%</p>
                </div>
            </div>
            <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-blue-500" style="width: {{ $trainingProgress->percent }}%"></div>
            </div>
            <a href="{{ route('user.learning-progress.index') }}" class="mt-3 flex items-center gap-1 text-xs font-semibold text-brand-700 hover:text-brand-800">
                Continue Learning <x-icon name="chevron-right" class="h-3 w-3" />
            </a>
        </div>

        <div class="card p-5">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white">
                    <x-icon name="users" class="h-5 w-5" />
                </span>
                <div class="min-w-0">
                    <p class="text-xs text-slate-400">Total Leads</p>
                    <p class="text-xl font-extrabold text-slate-800">{{ $leadStats->total }}</p>
                </div>
            </div>
            @if ($leadStats->new_this_week > 0)
                <p class="mt-2 text-xs font-medium text-emerald-600">↑ {{ $leadStats->new_this_week }} this week</p>
            @endif
            <a href="{{ route('user.leads.index') }}" class="mt-3 flex items-center gap-1 text-xs font-semibold text-brand-700 hover:text-brand-800">
                Go to Leads <x-icon name="chevron-right" class="h-3 w-3" />
            </a>
        </div>

        <div class="card p-5">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-amber-500 text-white">
                    <x-icon name="coin" class="h-5 w-5" />
                </span>
                <div class="min-w-0">
                    <p class="text-xs text-slate-400">Sales / Conversions</p>
                    <p class="text-xl font-extrabold text-slate-800">₹{{ number_format($leadStats->sales_this_month, 0) }}</p>
                </div>
            </div>
            @if (! is_null($leadStats->sales_change_percent))
                <p class="mt-2 text-xs font-medium {{ $leadStats->sales_change_percent >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ $leadStats->sales_change_percent >= 0 ? '↑' : '↓' }} {{ abs($leadStats->sales_change_percent) }}% this month
                </p>
            @endif
            <a href="{{ route('user.leads.index') }}" class="mt-3 flex items-center gap-1 text-xs font-semibold text-brand-700 hover:text-brand-800">
                View Sales <x-icon name="chevron-right" class="h-3 w-3" />
            </a>
        </div>

        <div class="card p-5">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-violet-600 text-white">
                    <x-icon name="gift" class="h-5 w-5" />
                </span>
                <div class="min-w-0">
                    <p class="text-xs text-slate-400">Earnings (This Month)</p>
                    <p class="text-xl font-extrabold text-slate-800">₹{{ number_format($leadStats->earnings_this_month, 0) }}</p>
                </div>
            </div>
            @if (! is_null($leadStats->earnings_change_percent))
                <p class="mt-2 text-xs font-medium {{ $leadStats->earnings_change_percent >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ $leadStats->earnings_change_percent >= 0 ? '↑' : '↓' }} {{ abs($leadStats->earnings_change_percent) }}% this month
                </p>
            @endif
            <a href="{{ route('user.incentives.index') }}" class="mt-3 flex items-center gap-1 text-xs font-semibold text-brand-700 hover:text-brand-800">
                View Earnings <x-icon name="chevron-right" class="h-3 w-3" />
            </a>
        </div>
    </div>

    {{-- Quick actions + banner --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="card p-5 lg:col-span-2">
            <p class="mb-4 font-bold text-slate-800">Quick Actions</p>
            <div class="grid grid-cols-3 gap-3 sm:grid-cols-6">
                @foreach ([
                    ['icon' => 'users', 'label' => 'My Leads', 'route' => route('user.leads.index')],
                    ['icon' => 'academic-cap', 'label' => 'Start Training', 'route' => route('user.training')],
                    ['icon' => 'download', 'label' => 'Documents', 'route' => route('user.documents.index')],
                    ['icon' => 'gift', 'label' => 'View Contest', 'route' => route('user.contests.index')],
                    ['icon' => 'trending-up', 'label' => 'My Performance', 'route' => route('user.leaderboard.index')],
                    ['icon' => 'help-circle', 'label' => 'Raise Ticket', 'route' => route('user.support.tickets.index')],
                ] as $action)
                    <a href="{{ $action['route'] }}" class="flex flex-col items-center gap-2 rounded-xl border border-slate-100 p-3 text-center transition hover:border-brand-200 hover:bg-brand-50/40">
                        <x-icon name="{{ $action['icon'] }}" class="h-7 w-7 text-brand-600" stroke-width="2" />
                        <span class="text-xs font-medium text-slate-600">{{ $action['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="card flex items-center justify-center p-3">
            <img src="{{ asset('images/dashboard.png') }}" alt="One Ecosystem. Endless Opportunities. Learn, Sell, Grow." class="w-full rounded-xl">
        </div>
    </div>

    {{-- Contest Progress | Today's Tasks | Upcoming Events --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="font-bold text-slate-800">Contest Progress</p>
                <a href="{{ route('user.contest-tracker.index') }}" class="flex items-center gap-1 text-xs font-semibold text-brand-700 hover:text-brand-800">View All <x-icon name="chevron-right" class="h-3 w-3" /></a>
            </div>

            @if ($contestProgress)
                <p class="mt-3 text-sm font-semibold text-slate-700">{{ $contestProgress->contest->name }}</p>
                <p class="text-xs text-slate-400">{{ $contestProgress->contest->starts_at->format('d M') }} &ndash; {{ $contestProgress->contest->ends_at->format('d M Y') }}</p>

                <div class="mt-4 flex items-center gap-5">
                    <div class="relative flex h-24 w-24 shrink-0 items-center justify-center">
                        <svg class="h-24 w-24 -rotate-90" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="{{ $ringRadius }}" fill="none" stroke="#ede9fe" stroke-width="10" />
                            <circle cx="50" cy="50" r="{{ $ringRadius }}" fill="none" stroke="#7c3aed" stroke-width="10" stroke-linecap="round" stroke-dasharray="{{ $ringCircumference }}" stroke-dashoffset="{{ $contestOffset }}" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-lg font-extrabold text-slate-800">{{ $contestProgress->percent }}%</span>
                            <span class="text-[9px] text-slate-400">Progress</span>
                        </div>
                    </div>
                    <div class="space-y-1.5 text-sm">
                        <p class="text-slate-500">Your Rank <span class="font-bold text-slate-800">{{ $contestProgress->rank ?? '—' }}/{{ $contestProgress->total_participants }}</span></p>
                        <p class="text-slate-500">{{ $contestProgress->contest->formatAmount($contestProgress->achieved) }} / {{ $contestProgress->contest->targetLabel() }}</p>
                        @if ($contestProgress->contest->reward)
                            <p class="flex items-center gap-1 text-xs font-semibold text-emerald-600"><x-icon name="gift" class="h-3.5 w-3.5" /> {{ $contestProgress->contest->reward }}</p>
                        @endif
                    </div>
                </div>
            @else
                <div class="mt-6 text-center text-sm text-slate-400">
                    You haven't joined a contest yet.
                    <a href="{{ route('user.contests.index') }}" class="block font-semibold text-brand-700 hover:underline">Browse Contests →</a>
                </div>
            @endif
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="font-bold text-slate-800">Today's Tasks</p>
            </div>
            <div class="mt-3 divide-y divide-slate-100">
                @foreach ($tasks as $task)
                    <a href="{{ $task['route'] }}" class="flex items-center justify-between gap-3 py-2.5 text-sm transition hover:text-brand-700">
                        <span class="flex items-center gap-2 text-slate-600">
                            <x-icon name="check-circle" class="h-4 w-4 text-slate-300" />
                            {{ $task['label'] }}
                        </span>
                        <span class="flex items-center gap-1 shrink-0">
                            <span class="font-semibold {{ $task['count'] > 0 ? 'text-red-600' : 'text-slate-400' }}">{{ $task['count'] }}</span>
                            <x-icon name="chevron-right" class="h-3.5 w-3.5 text-slate-300" />
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="font-bold text-slate-800">Upcoming Training / Webinars</p>
                <a href="{{ route('user.events.index') }}" class="flex items-center gap-1 text-xs font-semibold text-brand-700 hover:text-brand-800">View All <x-icon name="chevron-right" class="h-3 w-3" /></a>
            </div>

            @if ($upcomingEvents->isEmpty())
                <p class="mt-6 text-center text-sm text-slate-400">No upcoming events right now.</p>
            @else
                <div class="mt-3 space-y-3">
                    @foreach ($upcomingEvents as $event)
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 shrink-0 flex-col items-center justify-center rounded-lg bg-brand-50 leading-none text-brand-700">
                                <span class="text-sm font-extrabold">{{ $event->starts_at->format('d') }}</span>
                                <span class="text-[9px] font-semibold uppercase">{{ $event->starts_at->format('M') }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ $event->title }}</p>
                                <p class="truncate text-xs text-slate-400">{{ $event->starts_at->format('h:i A') }}@if ($event->ends_at) &ndash; {{ $event->ends_at->format('h:i A') }}@endif</p>
                            </div>
                            <a href="{{ route('user.events.index') }}" class="shrink-0 rounded-lg border border-brand-200 px-3 py-1.5 text-xs font-semibold text-brand-700 hover:bg-brand-50">
                                {{ $event->isRegisteredBy($user) ? 'Registered' : 'Register' }}
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Announcements | Learning Progress | Top Performers --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="font-bold text-slate-800">Latest Announcements</p>
                <a href="{{ route('user.announcements.index') }}" class="flex items-center gap-1 text-xs font-semibold text-brand-700 hover:text-brand-800">View All <x-icon name="chevron-right" class="h-3 w-3" /></a>
            </div>

            @if ($announcements->isEmpty())
                <p class="mt-6 text-center text-sm text-slate-400">No announcements yet.</p>
            @else
                <div class="mt-3 space-y-3">
                    @foreach ($announcements as $announcement)
                        <div class="flex items-start gap-2.5">
                            <x-icon name="bell" class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" />
                            <div class="min-w-0">
                                <p class="truncate text-sm text-slate-700">{{ $announcement->title }}</p>
                                <p class="text-xs text-slate-400">{{ $announcement->published_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="font-bold text-slate-800">My Learning Progress</p>
                <a href="{{ route('user.learning-progress.index') }}" class="flex items-center gap-1 text-xs font-semibold text-brand-700 hover:text-brand-800">View All <x-icon name="chevron-right" class="h-3 w-3" /></a>
            </div>

            @if ($learningProgress->isEmpty())
                <p class="mt-6 text-center text-sm text-slate-400">No courses assigned yet.</p>
            @else
                <div class="mt-3 space-y-3">
                    @foreach ($learningProgress as $course)
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                                <x-icon name="academic-cap" class="h-4 w-4" />
                            </span>
                            <p class="min-w-0 flex-1 truncate text-sm text-slate-600">{{ $course->title }}</p>
                            <div class="h-1.5 w-16 shrink-0 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-brand-600" style="width: {{ $course->progress->percent }}%"></div>
                            </div>
                            <span class="w-9 shrink-0 text-right text-xs font-semibold text-slate-500">{{ $course->progress->percent }}%</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="font-bold text-slate-800">Top Performers</p>
                <a href="{{ route('user.leaderboard.index') }}" class="flex items-center gap-1 text-xs font-semibold text-brand-700 hover:text-brand-800">View Leaderboard <x-icon name="chevron-right" class="h-3 w-3" /></a>
            </div>

            <div class="mt-3 space-y-2.5">
                @foreach ($topPerformers->top as $row)
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $row->rank === 1 ? 'bg-amber-400 text-white' : ($row->rank === 2 ? 'bg-slate-300 text-white' : 'bg-orange-300 text-white') }}">{{ $row->rank }}</span>
                            <span class="truncate text-sm text-slate-700">{{ $row->user->name }}</span>
                        </div>
                        <span class="shrink-0 text-sm font-semibold text-slate-600">{{ number_format($row->points) }} pts</span>
                    </div>
                @endforeach

                @if ($topPerformers->me)
                    <div class="flex items-center justify-between gap-3 rounded-lg bg-brand-50 px-2 py-1.5">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-600 text-xs font-bold text-white">{{ $topPerformers->me->rank }}</span>
                            <span class="truncate text-sm font-semibold text-brand-700">You ({{ $topPerformers->me->user->name }})</span>
                        </div>
                        <span class="shrink-0 text-sm font-semibold text-brand-700">{{ number_format($topPerformers->me->points) }} pts</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-layout>
