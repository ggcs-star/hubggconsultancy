<x-layout title="{{ $client->name }}" subtitle="Full profile details">

    @php
        $salespersonMap = ['none' => 'badge-slate', 'pending' => 'badge-amber', 'approved' => 'badge-green', 'rejected' => 'badge-slate'];
        $statusMap = ['active' => 'badge-green', 'inactive' => 'badge-slate', 'blocked' => 'bg-red-50 text-red-600'];
        $assessmentStatusMap = [
            'not_started' => ['label' => 'Not Started', 'class' => 'badge-slate'],
            'in_progress' => ['label' => 'In Progress', 'class' => 'badge-amber'],
            'pending_review' => ['label' => 'Pending Review', 'class' => 'badge-amber'],
            'passed' => ['label' => 'Passed', 'class' => 'badge-green'],
            'failed' => ['label' => 'Failed', 'class' => 'bg-red-50 text-red-600'],
        ];
    @endphp

    <a href="{{ route('admin.clients') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
        <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
        Back to Users
    </a>

    <div class="card mt-4 p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-700 text-xl font-bold text-white">
                    {{ strtoupper(substr($client->name, 0, 1)) }}
                </span>
                <div>
                    <p class="text-lg font-bold text-slate-800">{{ $client->name }}</p>
                    <p class="text-sm text-slate-400">{{ $client->email }}</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="badge {{ $client->profile_completed ? 'badge-green' : 'badge-slate' }}">
                    {{ $client->profile_completed ? 'Profile Complete' : 'Profile Incomplete' }}
                </span>
                <span class="badge {{ $salespersonMap[$client->salesperson_status] ?? 'badge-slate' }}">
                    Salesperson: {{ ucfirst($client->salesperson_status) }}
                </span>
                <span class="badge {{ $statusMap[$client->status] ?? 'badge-slate' }}">
                    {{ ucfirst($client->status) }}
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                    <x-icon name="trending-up" class="h-3.5 w-3.5" />
                    {{ $points->earned }}/{{ $points->total }} pts
                </span>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6">
                <h2 class="font-bold text-slate-800">Basic Information</h2>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Full Name</p>
                        <p class="mt-0.5 text-sm text-slate-700">{{ $client->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Phone Number</p>
                        <p class="mt-0.5 text-sm text-slate-700">{{ $client->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Highest Qualification</p>
                        <p class="mt-0.5 text-sm text-slate-700">{{ $client->highest_qualification ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Institution Name</p>
                        <p class="mt-0.5 text-sm text-slate-700">{{ $client->institution_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Field of Study</p>
                        <p class="mt-0.5 text-sm text-slate-700">{{ $client->field_of_study ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Year of Completion</p>
                        <p class="mt-0.5 text-sm text-slate-700">{{ $client->education_year ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h2 class="font-bold text-slate-800">Address Details</h2>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Full Address</p>
                        <p class="mt-0.5 text-sm text-slate-700">{{ $client->address ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Pincode</p>
                        <p class="mt-0.5 text-sm text-slate-700">{{ $client->pincode ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">City</p>
                        <p class="mt-0.5 text-sm text-slate-700">{{ $client->city ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">State</p>
                        <p class="mt-0.5 text-sm text-slate-700">{{ $client->state ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Country</p>
                        <p class="mt-0.5 text-sm text-slate-700">{{ $client->country ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h2 class="font-bold text-slate-800">Interest</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    @forelse ($client->interests as $interest)
                        <span class="badge badge-slate">{{ $interest->name }}</span>
                    @empty
                        <span class="text-sm text-slate-400">No interests selected.</span>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h2 class="font-bold text-slate-800">Onboarding Assessment</h2>
                <p class="mt-3 text-2xl font-extrabold text-slate-800">
                    {{ $assessmentScore->earned_points }}/{{ $assessmentScore->total_points }}
                    <span class="text-sm font-medium text-slate-400">pts</span>
                </p>
                <span class="badge {{ $assessmentStatusMap[$assessmentScore->status]['class'] }}">{{ $assessmentStatusMap[$assessmentScore->status]['label'] }}</span>
                @if ($assessmentScore->attempted)
                    <a href="{{ route('admin.onboarding-assessment.results.show', $client) }}" class="mt-3 block text-sm font-semibold text-brand-700 hover:text-brand-800">View full breakdown</a>
                @endif
            </div>

            <div class="card p-6">
                <h2 class="font-bold text-slate-800">Assigned Courses</h2>
                <div class="mt-3 space-y-1.5">
                    @forelse ($client->assignedCourses as $course)
                        <p class="text-sm text-slate-700">{{ $course->title }}</p>
                    @empty
                        <p class="text-sm text-slate-400">No courses assigned yet.</p>
                    @endforelse
                </div>
                <a href="{{ route('admin.salesperson-applications') }}" class="mt-3 block text-sm font-semibold text-brand-700 hover:text-brand-800">Manage assignments</a>
            </div>

            <div class="card p-6">
                <h2 class="font-bold text-slate-800">Account</h2>
                <div class="mt-3 space-y-2 text-sm">
                    <p class="text-slate-500">Joined <span class="text-slate-700">{{ $client->created_at->format('d M Y') }}</span></p>
                </div>
            </div>
        </div>
    </div>

</x-layout>
