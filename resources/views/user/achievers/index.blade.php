<x-layout title="Achievers" title-icon="star" subtitle="Users who recently completed a course or certification">

    @php
        $tabs = ['all' => 'All Time', 'today' => 'Today', 'week' => 'This Week', 'month' => 'This Month'];
    @endphp

    <div class="flex flex-wrap gap-2">
        @foreach ($tabs as $key => $label)
            <a href="{{ route('user.achievers.index', $key === 'all' ? [] : ['period' => $key]) }}"
                class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $period === $key ? 'bg-brand-600 text-white' : 'bg-white text-slate-500 hover:bg-slate-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($certificates as $certificate)
            @php
                $score = $certificate->course?->scoreFor($certificate->user);
                $percent = $score?->percent;
                $earnedPoints = $score?->earned_points;
                $badge = \App\Models\Certificate::badgeFor($percent);
            @endphp
            <div class="card flex flex-col p-5">
                <div class="flex items-start gap-3">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-700 text-base font-bold text-white">
                        {{ strtoupper(substr($certificate->user->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-slate-800">{{ $certificate->user->name }}</p>
                        @if ($certificate->user->designation)
                            <p class="text-sm text-slate-400">{{ $certificate->user->designation }}</p>
                        @endif
                    </div>
                    <span class="badge {{ $badge['class'] }} shrink-0">{{ $badge['label'] }}</span>
                </div>

                <p class="mt-3 text-sm text-slate-600">Completed <span class="font-semibold text-slate-800">{{ $certificate->course?->title }}</span></p>

                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm text-slate-500">
                    @if ($percent !== null)
                        <span>🎯 Score: {{ $percent }}%</span>
                    @endif
                    <span>⭐ {{ number_format($earnedPoints ?? 0) }} Points</span>
                </div>

                <p class="mt-3 text-xs text-slate-400">Achieved: {{ $certificate->issued_at->format('d M Y') }}</p>
            </div>
        @empty
            <div class="col-span-full card px-6 py-16 text-center text-slate-400">
                No achievements in this period yet.
            </div>
        @endforelse
    </div>

    @if ($certificates->hasPages())
        <div class="mt-6">
            {{ $certificates->links() }}
        </div>
    @endif

</x-layout>
