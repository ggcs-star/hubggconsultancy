<x-layout title="Dashboard" subtitle="Track your onboarding journey with GG Hub">

    {{-- Static "Grey to Glory" video — same click-to-play card UI as the Resources
         library, but not backed by the Resource model (fixed content, no quiz). --}}
    <div class="card flex flex-col overflow-hidden border-brand-100 bg-brand-50 sm:flex-row sm:min-h-[30rem]"
        x-data="{
            playing: false,
            videoIds: { english: 'Jj2QrOFjbkQ', hindi: 'XTz1w3cfR_U' },
            play(language) {
                window.stopResourcePlayer();
                this.playing = true;
                this.$nextTick(() => window.startResourcePlayer(this.videoIds[language], []));
            },
            stop() {
                window.stopResourcePlayer();
                this.playing = false;
            },
            toggleFullscreen() {
                const el = document.getElementById('dashboard-video-frame');
                if (! document.fullscreenElement) {
                    el?.requestFullscreen?.();
                } else {
                    document.exitFullscreen?.();
                }
            },
        }">
        <div id="dashboard-video-frame" class="relative h-72 shrink-0 bg-black sm:h-auto sm:w-[62%] sm:self-stretch">
            <template x-if="! playing">
                <div class="group relative h-full w-full cursor-pointer" x-on:click="play('english')">
                    <img src="https://img.youtube.com/vi/Jj2QrOFjbkQ/maxresdefault.jpg" alt="Grey to Glory" class="h-full w-full object-cover">
                    <div class="absolute inset-0 flex items-center justify-center bg-black/25 transition group-hover:bg-black/40">
                        <span class="flex h-20 w-20 items-center justify-center rounded-full bg-white/90 text-brand-700 shadow-lg transition group-hover:scale-105">
                            <x-icon name="play-circle" class="h-12 w-12" />
                        </span>
                    </div>
                </div>
            </template>

            <template x-if="playing">
                <div class="relative h-full w-full">
                    <div id="resource-youtube-player" class="h-full w-full"></div>
                    {{-- YouTube's own title/channel overlay can't be disabled via the embed API
                         (the old showinfo param was retired) — this masks it instead. --}}
                    <div class="pointer-events-none absolute inset-x-0 top-0 z-[5] h-16 bg-gradient-to-b from-black/95 via-black/60 to-transparent"></div>
                    <div class="absolute right-3 top-3 z-10 flex items-center gap-1.5">
                        <button type="button" x-on:click="toggleFullscreen()" title="Full screen" class="rounded-md bg-black/60 p-2 text-white hover:bg-black/80">
                            <x-icon name="maximize" class="h-5 w-5" />
                        </button>
                        <button type="button" x-on:click="stop()" title="Close" class="rounded-md bg-black/60 p-2 text-white hover:bg-black/80">
                            <x-icon name="x" class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <div class="flex flex-1 flex-col justify-center p-8 sm:p-10">
            <h3 class="text-2xl font-bold text-slate-800 sm:text-3xl">Grey to Glory – Our Journey of Resilience</h3>
            <p class="mt-4 max-w-xl text-base text-slate-500">
                From the darkest clouds to the brightest skies — Grey to Glory is the story of how our company faced storms, setbacks, and challenges, yet rose stronger than ever. It's a journey of resilience, determination, and the unwavering spirit that turned struggles into success.
            </p>
            <div class="mt-6 flex gap-4">
                <button type="button" x-on:click="play('english')" class="btn-primary">▶️ English</button>
                <button type="button" x-on:click="play('hindi')" class="btn-primary">▶️ Hindi</button>
            </div>
        </div>
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
