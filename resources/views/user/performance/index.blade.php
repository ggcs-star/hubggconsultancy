<x-layout title="My Performance" title-icon="trending-up" subtitle="Your points, learning, and sales activity">

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Points</p>
            <p class="mt-2 text-2xl font-extrabold text-slate-800">{{ number_format($points) }}</p>
            <p class="mt-1 text-xs text-emerald-600">+{{ number_format($pointsThisMonth) }} this month</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Certifications</p>
            <p class="mt-2 text-2xl font-extrabold text-slate-800">{{ $certificatesTotal }}</p>
            <p class="mt-1 text-xs text-emerald-600">+{{ $certificatesThisMonth }} this month</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Avg. Quiz Score</p>
            <p class="mt-2 text-2xl font-extrabold text-slate-800">{{ $avgScore !== null ? $avgScore . '%' : '—' }}</p>
            <p class="mt-1 text-xs text-slate-400">Across certified courses</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Leads Won</p>
            <p class="mt-2 text-2xl font-extrabold text-slate-800">{{ $leadsWonTotal }}</p>
            <p class="mt-1 text-xs text-emerald-600">+{{ $leadsWonThisMonth }} this month</p>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="card p-6 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Current Tier</p>
                    <p class="mt-1 text-xl font-bold text-slate-800">{{ $tier }}</p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                    <x-icon name="sparkles" class="h-6 w-6" />
                </span>
            </div>

            @if ($tierProgress['nextTier'])
                <div class="mt-4">
                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-brand-600" style="width: {{ $tierProgress['percent'] }}%"></div>
                    </div>
                    <p class="mt-2 text-xs text-slate-400">
                        {{ number_format($tierProgress['pointsToNext']) }} points to {{ $tierProgress['nextTier'] }}
                    </p>
                </div>
            @else
                <p class="mt-4 text-xs text-slate-400">You've reached the highest tier.</p>
            @endif

            <a href="{{ route('user.hall-of-fame.index') }}" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-700 hover:text-brand-800">
                See how you rank in the Hall of Fame <x-icon name="chevron-right" class="h-3.5 w-3.5" />
            </a>
        </div>

        <div class="card p-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">This Month</p>
            <div class="mt-3 space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Active learning days</span>
                    <span class="font-semibold text-slate-800">{{ $activeDaysThisMonth }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Certifications earned</span>
                    <span class="font-semibold text-slate-800">{{ $certificatesThisMonth }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Leads won</span>
                    <span class="font-semibold text-slate-800">{{ $leadsWonThisMonth }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Recent Achievements</h2>
        </div>

        @if ($recentCertificates->isEmpty())
            <div class="px-6 py-16 text-center text-sm text-slate-400">
                Complete a course to see your achievements here.
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($recentCertificates as $certificate)
                    @php
                        $score = $certificate->course?->scoreFor(auth()->user());
                        $badge = \App\Models\Certificate::badgeFor($score?->percent);
                    @endphp
                    <div class="flex items-center justify-between gap-3 px-5 py-4">
                        <div>
                            <p class="font-semibold text-slate-800">Completed {{ $certificate->course?->title }}</p>
                            <p class="mt-0.5 text-xs text-slate-400">{{ $certificate->issued_at->format('d M Y') }}</p>
                        </div>
                        <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</x-layout>
