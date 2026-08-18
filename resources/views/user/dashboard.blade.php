<x-layout title="Dashboard" subtitle="Track your onboarding journey with Pre Sales School">

    <div class="card bg-gradient-to-r from-brand-700 to-brand-600 p-6 text-white">
        <p class="text-sm text-brand-100">Welcome back,</p>
        <h2 class="text-2xl font-extrabold">{{ $user->name }}</h2>
        <p class="mt-2 max-w-xl text-sm text-brand-100">
            Complete your profile, take the onboarding assessment, and work through your training to get certified.
        </p>
    </div>

    @php
        $steps = [
            ['label' => 'Onboard', 'done' => true],
            ['label' => 'Complete Profile', 'done' => $user->profile_completed],
            ['label' => 'Take Assessment', 'done' => $assessmentScore->attempted],
            ['label' => 'Get Approved', 'done' => $user->salesperson_status === 'approved'],
        ];
    @endphp

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card icon="academic-cap" color="primary" :value="$stats['enrolled_courses']" label="Enrolled Courses" />
        <x-stat-card icon="check-circle" color="chart-4" :value="$stats['overall_completion'] . '%'" label="Overall Completion" :trend="$activityChart['data']" />
        <x-stat-card icon="badge" color="success" :value="$stats['certificates']" label="Certificates Earned" />
        <x-stat-card icon="briefcase" color="warning" :value="$stats['assessment_percent'] . '%'" label="Assessment Score" />
    </div>

    <div class="card mt-6 p-6">
        <h3 class="font-bold text-slate-800">Your Progress</h3>
        <div class="mt-6 flex items-center">
            @foreach ($steps as $i => $step)
                <div class="flex flex-1 flex-col items-center text-center">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-bold
                        {{ $step['done'] ? 'bg-brand-700 text-white' : 'border-2 border-slate-200 text-slate-400' }}">
                        @if ($step['done'])
                            <x-icon name="check-circle" class="h-5 w-5" />
                        @else
                            {{ $i + 1 }}
                        @endif
                    </div>
                    <p class="mt-2 text-xs font-medium text-slate-500">{{ $step['label'] }}</p>
                </div>
                @if (!$loop->last)
                    <div class="-mt-6 h-0.5 flex-1 {{ $step['done'] ? 'bg-brand-700' : 'bg-slate-200' }}"></div>
                @endif
            @endforeach
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="card p-5 lg:col-span-2">
            <h3 class="font-bold text-slate-800">Progress by Course</h3>
            <div class="mt-4 h-64">
                <canvas id="courseProgressChart"></canvas>
            </div>
        </div>
        <div class="card p-5">
            <h3 class="font-bold text-slate-800">Quiz Performance</h3>
            <div class="mt-4 h-64">
                <canvas id="quizChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card mt-6 p-5">
        <h3 class="font-bold text-slate-800">Learning Activity (Last 14 Days)</h3>
        <div class="mt-4 h-56">
            <canvas id="activityChart"></canvas>
        </div>
    </div>

    <div class="card mt-6">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">My Courses</h2>
            <a href="{{ route('user.training') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-400">
                        <th class="px-5 py-3 font-semibold">Course</th>
                        <th class="px-5 py-3 font-semibold">Modules / Lessons</th>
                        <th class="px-5 py-3 font-semibold">Progress</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($courseProgress as $cp)
                        <tr>
                            <td class="px-5 py-3.5 font-medium text-slate-700">{{ $cp->course->title }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $cp->modules_count }} modules · {{ $cp->completed_lessons }}/{{ $cp->total_lessons }} lessons</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-28 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-brand-600" style="width: {{ $cp->percent }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-500">{{ $cp->percent }}%</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $courseStatusMap = ['Not started' => 'badge-slate', 'In progress' => 'badge-amber', 'Completed' => 'badge-green'];
                                @endphp
                                <span class="badge {{ $courseStatusMap[$cp->status] }}">{{ $cp->status }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('user.courses.show', $cp->course) }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Continue</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">You are not enrolled in any courses yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('user.profile') }}" class="card p-5 transition hover:border-brand-200 hover:shadow-md">
            <span class="rounded-xl bg-brand-50 p-2.5 text-brand-700 inline-flex"><x-icon name="user" /></span>
            <p class="mt-4 font-semibold text-slate-800">My Profile</p>
            <p class="text-sm text-slate-400">{{ $user->profile_completed ? 'Completed' : 'Complete your profile' }}</p>
        </a>
        @php
            $assessmentStatusLabels = [
                'not_started' => 'Not started',
                'in_progress' => 'In progress',
                'pending_review' => 'Pending review',
                'passed' => 'Passed',
                'failed' => 'Not passed',
            ];
        @endphp
        <a href="{{ route('user.onboarding-assessment.index') }}" class="card p-5 transition hover:border-brand-200 hover:shadow-md">
            <span class="rounded-xl bg-amber-50 p-2.5 text-amber-600 inline-flex"><x-icon name="check-circle" /></span>
            <p class="mt-4 font-semibold text-slate-800">Onboarding Assessment</p>
            <p class="text-sm text-slate-400">Status: {{ $assessmentStatusLabels[$assessmentScore->status] }}</p>
        </a>
        <a href="{{ route('user.training') }}" class="card p-5 transition hover:border-brand-200 hover:shadow-md">
            <span class="rounded-xl bg-sky-50 p-2.5 text-sky-600 inline-flex"><x-icon name="academic-cap" /></span>
            <p class="mt-4 font-semibold text-slate-800">Training / LMS</p>
            <p class="text-sm text-slate-400">Courses & lessons</p>
        </a>
        <a href="{{ route('user.manuals') }}" class="card p-5 transition hover:border-brand-200 hover:shadow-md">
            <span class="rounded-xl bg-emerald-50 p-2.5 text-emerald-600 inline-flex"><x-icon name="document" /></span>
            <p class="mt-4 font-semibold text-slate-800">Sales Manuals</p>
            <p class="text-sm text-slate-400">PPTs & documents</p>
        </a>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                PSSCharts.renderBarChart('courseProgressChart', @json($progressChart['labels']), @json($progressChart['data']), { color: PSSCharts.PALETTE.brand });
                PSSCharts.renderLineChart('activityChart', @json($activityChart['labels']), @json($activityChart['data']), { label: 'Lessons completed' });
                PSSCharts.renderDoughnutChart('quizChart', @json($quizChart['labels']), @json($quizChart['data']), {
                    colors: [PSSCharts.PALETTE.success, PSSCharts.PALETTE.danger, PSSCharts.PALETTE.slate],
                });
            });
        </script>
    @endpush

</x-layout>
