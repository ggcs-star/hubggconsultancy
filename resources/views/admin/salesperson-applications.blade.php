<x-layout title="Salesperson Applications" subtitle="Review and approve clients who applied to become salespeople">

    @php
        $statusMap = ['pending' => 'badge-amber', 'approved' => 'badge-green', 'rejected' => 'badge-slate'];
        $applicationFilterFields = ['search', 'status'];
    @endphp

    <form method="GET" class="flex flex-wrap items-end gap-4 rounded-xl border border-slate-200 bg-white p-4">
        <div class="w-full sm:w-64">
            <label class="form-label">Search</label>
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..." class="form-input pl-10">
            </div>
        </div>

        <div class="w-full sm:w-48">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">All Statuses</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
            </select>
        </div>

        <button type="submit" class="btn-primary">Apply Filters</button>

        @if (request()->anyFilled($applicationFilterFields))
            <a href="{{ route('admin.salesperson-applications') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Reset Filters</a>
        @endif
    </form>

    <div class="mt-4 card">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-400">
                        <th class="px-5 py-3 font-semibold">Applicant</th>
                        <th class="px-5 py-3 font-semibold">Interest</th>
                        <th class="px-5 py-3 font-semibold">Score</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($applications as $applicant)
                        @php $score = $applicant->assessmentScore; @endphp
                        <tr>
                            <td class="flex items-center gap-3 px-5 py-3.5">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-700 text-xs font-semibold text-white">
                                    {{ strtoupper(substr($applicant->name, 0, 1)) }}
                                </span>
                                <div class="leading-tight">
                                    <p class="font-medium text-slate-700">{{ $applicant->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $applicant->email }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @forelse ($applicant->interests as $interest)
                                    <span class="badge badge-slate mb-1 mr-1">{{ $interest->name }}</span>
                                @empty
                                    <span class="text-slate-400">—</span>
                                @endforelse
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">
                                @if ($score->attempted)
                                    {{ $score->earned_points }}/{{ $score->total_points }} pts
                                    @if (! is_null($score->percent))
                                        <span class="text-xs text-slate-400">({{ $score->percent }}%)</span>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="badge {{ $statusMap[$applicant->salesperson_status] ?? 'badge-slate' }}">{{ ucfirst($applicant->salesperson_status) }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($applicant->salesperson_status === 'pending')
                                        <form method="POST" action="{{ route('admin.salesperson-applications.approve', $applicant) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-600 hover:bg-emerald-100">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.salesperson-applications.reject', $applicant) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100">Reject</button>
                                        </form>
                                    @endif
                                    <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'assign-courses-{{ $applicant->id }}')" class="rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-semibold text-brand-700 hover:bg-brand-100">
                                        Assign Courses
                                        @if ($applicant->assignedCourses->isNotEmpty())
                                            <span class="ml-1">({{ $applicant->assignedCourses->count() }})</span>
                                        @endif
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <x-modal name="assign-courses-{{ $applicant->id }}" max-width="md">
                            <form method="POST" action="{{ route('admin.salesperson-applications.courses.update', $applicant) }}">
                                @csrf
                                @method('PUT')

                                <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-5">
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-700">
                                        <x-icon name="academic-cap" class="h-5 w-5" />
                                    </span>
                                    <div class="min-w-0">
                                        <h2 class="text-lg font-bold text-slate-800">Assign Courses</h2>
                                        <p class="truncate text-sm text-slate-400">{{ $applicant->name }} will only see these courses in Training / LMS</p>
                                    </div>
                                </div>

                                <div class="max-h-80 space-y-2 overflow-y-auto px-6 py-5">
                                    @forelse ($allCourses as $course)
                                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 transition hover:border-brand-200 hover:bg-brand-50/60 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                                            <input
                                                type="checkbox"
                                                name="course_ids[]"
                                                value="{{ $course->id }}"
                                                @checked($applicant->assignedCourses->contains($course->id))
                                                class="h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                                            >
                                            <span class="font-medium">{{ $course->title }}</span>
                                        </label>
                                    @empty
                                        <p class="py-4 text-center text-sm text-slate-400">No published courses yet.</p>
                                    @endforelse
                                </div>

                                <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
                                    <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
                                    <button type="submit" class="btn-primary">Save Courses</button>
                                </div>
                            </form>
                        </x-modal>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">
                                @if (request()->anyFilled($applicationFilterFields))
                                    No applications match your filters.
                                @else
                                    No applications yet.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($applications->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

</x-layout>
