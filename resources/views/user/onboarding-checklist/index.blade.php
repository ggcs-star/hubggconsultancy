<x-layout title="Onboarding Checklist" title-icon="check-circle" subtitle="Work through these steps to get fully set up">

    @php
        $total = $totalCount;
        $percent = $total > 0 ? (int) round($completedCount / $total * 100) : 0;
        $ringRadius = 40;
        $ringCircumference = 2 * M_PI * $ringRadius;
        $ringOffset = $ringCircumference * (1 - $percent / 100);
    @endphp

    <div class="card flex flex-col items-center gap-5 p-6 sm:flex-row">
        <div class="relative flex h-28 w-28 shrink-0 items-center justify-center">
            <svg class="h-28 w-28 -rotate-90" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="{{ $ringRadius }}" fill="none" stroke="#ede9fe" stroke-width="10" />
                <circle cx="50" cy="50" r="{{ $ringRadius }}" fill="none" stroke="#7c3aed" stroke-width="10" stroke-linecap="round" stroke-dasharray="{{ $ringCircumference }}" stroke-dashoffset="{{ $ringOffset }}" />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-xl font-extrabold text-slate-800">{{ $percent }}%</span>
                <span class="text-[10px] text-slate-400">Complete</span>
            </div>
        </div>

        <div class="text-center sm:text-left">
            <p class="text-lg font-bold text-slate-800">
                @if ($total === 0)
                    Nothing to complete yet
                @elseif ($completedCount === $total)
                    You're all set! 🎉
                @else
                    {{ $completedCount }} of {{ $total }} steps completed
                @endif
            </p>
            <p class="mt-1 text-sm text-slate-500">
                @if ($total === 0)
                    Check back soon — your admin hasn't added any onboarding steps yet.
                @elseif ($completedCount === $total)
                    You've completed every onboarding step. Nice work!
                @else
                    Check off each step below as you complete it.
                @endif
            </p>
        </div>
    </div>

    <div class="mt-6 space-y-3">
        @forelse ($items as $item)
            @php $isDone = $completedIds->contains($item->id); @endphp
            <div class="card flex items-start gap-4 p-5 transition {{ $isDone ? 'bg-emerald-50/40' : '' }}">
                <form method="POST" action="{{ route('user.onboarding-checklist.toggle', $item) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" title="{{ $isDone ? 'Mark as not done' : 'Mark as done' }}" class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full border-2 transition {{ $isDone ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-300 text-transparent hover:border-brand-400' }}">
                        <x-icon name="check" class="h-4 w-4" />
                    </button>
                </form>

                <div class="min-w-0 flex-1">
                    <p class="font-semibold {{ $isDone ? 'text-slate-500 line-through' : 'text-slate-800' }}">{{ $item->title }}</p>
                    @if ($item->description)
                        <p class="mt-1 text-sm {{ $isDone ? 'text-slate-400' : 'text-slate-500' }}">{{ $item->description }}</p>
                    @endif
                    @if ($item->link)
                        <a href="{{ $item->link }}" target="_blank" rel="noopener" class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700 hover:text-brand-800">
                            <x-icon name="external-link" class="h-3.5 w-3.5" />
                            Open
                        </a>
                    @endif
                </div>

                @if ($isDone)
                    <span class="badge badge-green shrink-0">Done</span>
                @endif
            </div>
        @empty
            <div class="card p-10 text-center text-sm text-slate-400">No onboarding steps have been added yet.</div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $items->links() }}
    </div>

</x-layout>
