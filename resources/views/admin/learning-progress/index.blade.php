<x-layout title="Learning Progress" title-icon="academic-cap" subtitle="Course completion progress across every salesperson">

    <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative w-full sm:max-w-sm">
            <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="form-input pl-10">
        </div>

        <select name="course_id" class="form-input w-full sm:w-56" onchange="this.form.submit()">
            <option value="">All Courses</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>{{ $course->title }}</option>
            @endforeach
        </select>

        @if (request()->anyFilled(['search', 'course_id']))
            <a href="{{ route('admin.learning-progress.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Reset</a>
        @endif
    </form>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Progress by Salesperson</h2>
            <p class="mt-0.5 text-xs text-slate-400">{{ $users->total() }} {{ Str::plural('salesperson', $users->total()) }}</p>
        </div>

        @if ($rows->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                    <x-icon name="academic-cap" class="h-7 w-7 text-brand-600" />
                </div>
                <h3 class="mt-4 font-bold text-slate-800">No progress to show</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">
                    @if (request()->anyFilled(['search', 'course_id']))
                        Try changing your search or filters.
                    @else
                        Assign a course to a salesperson to start tracking progress.
                    @endif
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-400">
                            <th class="px-5 py-3 font-semibold">Salesperson</th>
                            <th class="px-5 py-3 font-semibold">Course</th>
                            <th class="px-5 py-3 font-semibold">Lessons</th>
                            <th class="px-5 py-3 font-semibold">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($rows as $row)
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-700 text-xs font-semibold text-white">
                                            {{ strtoupper(substr($row->user->name, 0, 1)) }}
                                        </span>
                                        <div>
                                            <p class="font-medium text-slate-800">{{ $row->user->name }}</p>
                                            <p class="text-xs text-slate-400">{{ $row->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">{{ $row->course->title }}</td>
                                <td class="px-5 py-3.5 text-slate-500">{{ $row->progress->completed_lessons }}/{{ $row->progress->total_lessons }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 w-32 overflow-hidden rounded-full bg-slate-100">
                                            <div class="h-full rounded-full {{ $row->progress->percent >= 100 ? 'bg-emerald-500' : 'bg-brand-600' }}" style="width: {{ $row->progress->percent }}%"></div>
                                        </div>
                                        <span class="w-10 shrink-0 text-sm font-semibold text-slate-600">{{ $row->progress->percent }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </div>

</x-layout>
