<x-layout title="Hall of Fame" title-icon="sparkles" subtitle="Celebrating the best of the best">

    @php
        $tabs = ['all' => 'All Time', 'month' => 'This Month', 'quarter' => 'This Quarter'];
        $medals = ['🥇', '🥈', '🥉'];
        $ordinals = ['1st', '2nd', '3rd'];
        $categories = [
            ['row' => $topSales ?? null, 'label' => 'Best Sales Performance', 'icon' => 'trending-up', 'metric' => fn ($row) => $row->leadsWon . ' leads won'],
            ['row' => $topLearning ?? null, 'label' => 'Highest Learning Score', 'icon' => 'academic-cap', 'metric' => fn ($row) => $row->learningScore . '% avg. score'],
            ['row' => $topCertifications ?? null, 'label' => 'Most Certifications', 'icon' => 'badge', 'metric' => fn ($row) => $row->certificates . ' certifications'],
            ['row' => $topConsistent ?? null, 'label' => 'Most Consistent Learner', 'icon' => 'calendar', 'metric' => fn ($row) => $row->activeDays . ' active days'],
        ];
    @endphp

    <div class="flex flex-wrap gap-2">
        @foreach ($tabs as $key => $label)
            <a href="{{ route('user.hall-of-fame.index', $key === 'all' ? [] : ['period' => $key]) }}"
                class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $period === $key ? 'bg-brand-600 text-white' : 'bg-white text-slate-500 hover:bg-slate-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="mt-6 card p-6">
        <h2 class="text-lg font-bold text-slate-800">Hall of Fame</h2>

        @if ($podium->isEmpty())
            <p class="mt-6 text-center text-sm text-slate-400">No ranked performers for this period yet.</p>
        @else
            <div class="mt-5 space-y-3">
                @foreach ($podium as $index => $row)
                    <div class="flex items-center gap-4 rounded-xl border border-slate-100 px-5 py-4">
                        <span class="text-2xl">{{ $medals[$index] }}</span>
                        <div class="flex-1">
                            <p class="font-bold text-slate-800">{{ $ordinals[$index] }} — {{ $row->user->name }}</p>
                            @if ($row->user->designation)
                                <p class="text-sm text-slate-400">{{ $row->user->designation }}</p>
                            @endif
                            <p class="mt-1 text-sm text-slate-500">
                                {{ number_format($row->points) }} Points
                                &middot; {{ $row->certificates }} Certifications
                                @if ($row->learningScore !== null)
                                    &middot; {{ $row->learningScore }}% Avg. Score
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="mt-6">
        <h2 class="text-lg font-bold text-slate-800">Top Achievers</h2>
        <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($categories as $category)
                <div class="card p-5">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                        <x-icon name="{{ $category['icon'] }}" class="h-5 w-5" />
                    </span>
                    <p class="mt-3 text-sm font-semibold text-slate-500">{{ $category['label'] }}</p>
                    @if ($category['row'])
                        <p class="mt-1 font-bold text-slate-800">{{ $category['row']->user->name }}</p>
                        <p class="text-sm text-slate-400">{{ ($category['metric'])($category['row']) }}</p>
                    @else
                        <p class="mt-1 text-sm text-slate-400">No data yet</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

</x-layout>
