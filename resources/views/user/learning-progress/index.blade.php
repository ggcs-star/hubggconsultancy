@php
    $badgeColors = ['bg-violet-50 text-violet-600', 'bg-blue-50 text-blue-600', 'bg-emerald-50 text-emerald-600', 'bg-amber-50 text-amber-600'];
@endphp

<x-layout title="Learning Progress" title-icon="academic-cap" subtitle="Your completion progress across every assigned course">

    <div class="card">
        @if ($courses->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                    <x-icon name="academic-cap" class="h-7 w-7 text-brand-600" />
                </div>
                <h3 class="mt-4 font-bold text-slate-800">No courses assigned yet</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">Once your admin assigns you a training course, your progress will show up here.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($courses as $course)
                    <div class="flex flex-wrap items-center gap-4 px-5 py-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $badgeColors[$loop->index % count($badgeColors)] }}">
                            <x-icon name="academic-cap" class="h-5 w-5" />
                        </span>

                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-slate-800">{{ $course->title }}</p>
                            <p class="mt-0.5 text-xs text-slate-400">{{ $course->progress->completed_lessons }}/{{ $course->progress->total_lessons }} lessons completed</p>
                        </div>

                        <div class="flex w-full items-center gap-3 sm:w-64">
                            <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-brand-600" style="width: {{ $course->progress->percent }}%"></div>
                            </div>
                            <span class="w-10 shrink-0 text-right text-sm font-semibold text-slate-600">{{ $course->progress->percent }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</x-layout>
