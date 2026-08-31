<x-layout title="Hall of Fame" title-icon="trophy" subtitle="Celebrating top performers and champions">

    @php
        $rankStyles = [
            1 => ['card' => 'bg-gradient-to-b from-amber-50 to-white ring-amber-200', 'badge' => 'bg-amber-500', 'ring' => 'ring-amber-400', 'points' => 'bg-amber-50 text-amber-700'],
            2 => ['card' => 'bg-gradient-to-b from-indigo-50 to-white ring-indigo-200', 'badge' => 'bg-indigo-500', 'ring' => 'ring-indigo-300', 'points' => 'bg-indigo-50 text-indigo-700'],
            3 => ['card' => 'bg-gradient-to-b from-orange-50 to-white ring-orange-200', 'badge' => 'bg-orange-500', 'ring' => 'ring-orange-300', 'points' => 'bg-orange-50 text-orange-700'],
        ];
        $defaultStyle = ['card' => 'bg-white ring-slate-100', 'badge' => 'bg-brand-600', 'ring' => 'ring-slate-200', 'points' => 'bg-slate-50 text-slate-700'];
    @endphp

    <form method="GET" class="flex flex-wrap items-center gap-2">
        <div class="relative w-full sm:w-48">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by name..." class="form-input pl-9">
        </div>
        <input type="number" name="points_min" min="0" value="{{ $pointsMin }}" placeholder="Min points" class="form-input w-full sm:w-32">
        <span class="text-slate-400">–</span>
        <input type="number" name="points_max" min="0" value="{{ $pointsMax }}" placeholder="Max points" class="form-input w-full sm:w-32">
        <input type="date" name="period_from" value="{{ $periodFrom }}" class="form-input w-full sm:w-40">
        <span class="text-slate-400">–</span>
        <input type="date" name="period_to" value="{{ $periodTo }}" class="form-input w-full sm:w-40">
        <button type="submit" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Filter</button>
        @if ($search || $pointsMin || $pointsMax || $periodFrom || $periodTo)
            <a href="{{ route('user.hall-of-fame.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Clear</a>
        @endif
    </form>

    @if ($entries->isEmpty())
        <div class="mt-6 card px-6 py-16 text-center text-slate-400">
            @if ($search || $pointsMin || $pointsMax || $periodFrom || $periodTo)
                No Hall of Fame entries match your filters.
            @else
                No Hall of Fame entries yet — check back soon.
            @endif
        </div>
    @else
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($entries as $index => $entry)
                @php $rank = $index + 1; $style = $rankStyles[$rank] ?? $defaultStyle; @endphp
                <div class="relative card ring-2 {{ $style['card'] }} p-6 text-center">
                    <span class="absolute left-4 top-4 flex h-7 w-7 items-center justify-center rounded-full {{ $style['badge'] }} text-xs font-bold text-white">
                        {{ $rank }}
                    </span>

                    <div class="relative mx-auto mt-4 w-fit">
                        @if ($rank === 1)
                            <x-icon name="sparkles" class="absolute -top-5 left-1/2 h-6 w-6 -translate-x-1/2 text-amber-400" />
                        @endif
                        @if ($entry->imageUrl())
                            <button type="button" x-data="" x-on:click="$dispatch('open-image-preview', '{{ $entry->imageUrl() }}')" title="View photo">
                                <img src="{{ $entry->imageUrl() }}" alt="" class="h-16 w-16 rounded-full object-cover ring-4 {{ $style['ring'] }} transition hover:opacity-90 sm:h-20 sm:w-20">
                            </button>
                        @else
                            <span class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-300 ring-4 {{ $style['ring'] }} sm:h-20 sm:w-20">
                                <x-icon name="user" class="h-8 w-8" />
                            </span>
                        @endif
                    </div>

                    <p class="mt-3 font-bold text-slate-800">{{ $entry->name }}</p>
                    @if ($entry->description)
                        <p class="text-sm text-slate-400">{{ $entry->description }}</p>
                    @endif
                    @if ($entry->periodLabel())
                        <p class="mt-0.5 text-xs text-slate-400">{{ $entry->periodLabel() }}</p>
                    @endif

                    <span class="mt-3 inline-flex items-center gap-1 rounded-full px-3 py-1 text-sm font-bold {{ $style['points'] }}">
                        <x-icon name="star" class="h-3.5 w-3.5" />
                        {{ number_format($entry->points) }} Points
                    </span>
                </div>
            @endforeach
        </div>
    @endif

    @include('partials.image-lightbox')

</x-layout>
