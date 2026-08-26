{{-- Expects $myTasks: the current user's tasks for the current month, soonest first. --}}
<div class="card p-5" x-data="{ tab: 'today' }">
    <div class="flex items-center justify-between">
        <p class="font-bold text-slate-800">To Do List</p>
        <a href="{{ route('tasks.index') }}" class="flex items-center gap-1 text-xs font-semibold text-brand-700 hover:text-brand-800">
            View Calendar <x-icon name="chevron-right" class="h-3 w-3" />
        </a>
    </div>

    <div class="mt-3 flex gap-2">
        <button type="button" x-on:click="tab = 'today'" class="rounded-full px-3 py-1 text-xs font-semibold transition" :class="tab === 'today' ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
            Today
        </button>
        <button type="button" x-on:click="tab = 'month'" class="rounded-full px-3 py-1 text-xs font-semibold transition" :class="tab === 'month' ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
            Month
        </button>
    </div>

    <div class="mt-3 divide-y divide-slate-100">
        @forelse ($myTasks as $task)
            <div class="flex items-center gap-3 py-2.5" x-show="tab === 'month' || {{ $task->date->isToday() ? 'true' : 'false' }}" x-cloak>
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                    <x-icon name="calendar" class="h-4 w-4" />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-slate-700">{{ $task->title }}</p>
                    <p class="text-xs text-slate-400">{{ $task->dateLabel() }}@if ($task->timeLabel()) &middot; {{ $task->timeLabel() }} @endif</p>
                </div>
            </div>
        @empty
            <p class="py-6 text-center text-sm text-slate-400">No tasks yet — add one from the calendar.</p>
        @endforelse

        @if ($myTasks->isNotEmpty() && $myTasks->doesntContain(fn ($task) => $task->date->isToday()))
            <p class="py-6 text-center text-sm text-slate-400" x-show="tab === 'today'" x-cloak>No tasks due today.</p>
        @endif
    </div>
</div>
