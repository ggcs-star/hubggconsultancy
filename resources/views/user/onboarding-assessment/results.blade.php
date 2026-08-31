<x-layout title="My Results" subtitle="Your onboarding assessment report card">

    @php
        $overallStatusCopy = [
            'in_progress' => ['label' => 'In Progress', 'class' => 'badge-amber', 'note' => 'Complete every quiz on the Assessments page to get your final score.'],
            'pending_review' => ['label' => 'Pending Review', 'class' => 'badge-amber', 'note' => 'Some of your answers are being reviewed by an admin. Your final score will update once grading is complete.'],
            'passed' => ['label' => 'Passed', 'class' => 'badge-green', 'note' => null],
            'failed' => ['label' => 'Not Passed', 'class' => 'bg-red-50 text-red-600', 'note' => null],
        ];
        $sectionStatusCopy = [
            'not_started' => ['label' => 'Not Attempted', 'class' => 'badge-slate'],
            'pending_review' => ['label' => 'Pending Review', 'class' => 'badge-amber'],
            'graded' => ['label' => 'Completed', 'class' => 'badge-green'],
        ];

        $ringPercent = $score->percent ?? 0;

        $totalQuestionsInAssessment = $score->quizzes->sum('total_question_count');
        $totalAnswered = $score->quizzes->sum('question_count');
        $totalIncorrect = max(0, $totalAnswered - $score->correct_count - $score->pending_count);
    @endphp

    @if (! $score->attempted)
        <div class="card p-10 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                <x-icon name="grid" class="h-7 w-7 text-brand-600" />
            </div>
            <h3 class="mt-4 font-bold text-slate-800">No results yet</h3>
            <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">You haven't submitted any assessment quizzes yet.</p>
            <a href="{{ route('user.onboarding-assessment.index') }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
                Go to Assessments
                <x-icon name="chevron-right" class="h-4 w-4" />
            </a>
        </div>
    @else
        <div class="card p-6">
            <div class="flex flex-wrap items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('favicon.png') }}" alt="Onboarding Assessment" class="h-28 w-28 shrink-0 object-contain sm:h-32 sm:w-32" />
                    <div>
                        <p class="font-bold text-slate-800">Onboarding Assessment</p>
                        <p class="mt-1 text-sm text-slate-400">{{ $score->attempted_quiz_count }}/{{ $score->quiz_count }} {{ Str::plural('section', $score->quiz_count) }} completed</p>

                        @if (isset($overallStatusCopy[$score->status]))
                            <span class="badge {{ $overallStatusCopy[$score->status]['class'] }} mt-3">{{ $overallStatusCopy[$score->status]['label'] }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    @if (! is_null($score->percent))
                        <div class="flex h-24 w-24 shrink-0 flex-col items-center justify-center rounded-xl border-2 border-brand-700 bg-gradient-to-br from-brand-600 to-brand-700 p-2 text-center shadow-lg shadow-brand-200">
                            <span class="text-lg font-extrabold text-white">{{ $ringPercent }}%</span>
                            <span class="text-[10px] font-medium leading-tight text-brand-100">Achieved Score</span>
                        </div>

                        <div class="hidden h-16 w-px shrink-0 bg-slate-200 sm:block"></div>
                    @endif

                    <div class="space-y-3 text-right">
                        <div>
                            <p class="text-2xl font-extrabold text-brand-700">{{ $score->earned_points }}/{{ $score->full_total_points }} <span class="text-sm font-medium text-slate-400">pts</span></p>
                            <p class="flex items-center justify-end gap-1 text-xs text-slate-400">Score Earned (So Far)</p>
                        </div>
                        @if (! is_null($score->percent))
                            <div>
                                <p class="text-2xl font-extrabold text-emerald-600">{{ $score->passing_score_percent }} <span class="text-sm font-medium text-slate-400">/ 100 %</span></p>
                                <p class="flex items-center justify-end gap-1 text-xs text-slate-400">Target Passing Score</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if (isset($overallStatusCopy[$score->status]) && $overallStatusCopy[$score->status]['note'])
                <div class="mt-4 flex items-start gap-2 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    <x-icon name="help-circle" class="mt-0.5 h-4 w-4 shrink-0" />
                    <p>{{ $overallStatusCopy[$score->status]['note'] }}</p>
                </div>
            @endif
        </div>

        {{-- Section-wise report card / marksheet --}}
        <div class="mt-6 card overflow-hidden">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="font-bold text-slate-800">Report Card</h2>
                <p class="mt-0.5 text-xs text-slate-400">Your performance across every section of the assessment</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-400">
                            <th class="px-6 py-3 font-semibold">Section</th>
                            <th class="px-4 py-3 text-center font-semibold">Questions</th>
                            <th class="px-4 py-3 text-center font-semibold">Correct</th>
                            <th class="px-4 py-3 text-center font-semibold">Incorrect</th>
                            <th class="px-4 py-3 text-center font-semibold">Pending</th>
                            <th class="px-4 py-3 text-center font-semibold">Score</th>
                            <th class="px-4 py-3 text-center font-semibold">%</th>
                            <th class="px-6 py-3 text-right font-semibold">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($score->quizzes as $quizScore)
                            @php
                                $sectionIncorrect = max(0, $quizScore->question_count - $quizScore->correct_count - $quizScore->pending_count);
                            @endphp
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-800">{{ $quizScore->quiz->title }}</p>
                                    @if ($quizScore->quiz->description)
                                        <p class="mt-0.5 text-xs text-slate-400">{{ $quizScore->quiz->description }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center text-slate-600">{{ $quizScore->total_question_count }}</td>
                                <td class="px-4 py-4 text-center font-semibold {{ $quizScore->attempted ? 'text-emerald-600' : 'text-slate-300' }}">{{ $quizScore->attempted ? $quizScore->correct_count : '—' }}</td>
                                <td class="px-4 py-4 text-center font-semibold {{ $quizScore->attempted && $sectionIncorrect > 0 ? 'text-red-500' : 'text-slate-300' }}">{{ $quizScore->attempted ? $sectionIncorrect : '—' }}</td>
                                <td class="px-4 py-4 text-center font-semibold {{ $quizScore->attempted && $quizScore->pending_count > 0 ? 'text-amber-500' : 'text-slate-300' }}">{{ $quizScore->attempted && $quizScore->pending_count > 0 ? $quizScore->pending_count : '—' }}</td>
                                <td class="px-4 py-4 text-center font-semibold text-slate-700">{{ $quizScore->attempted ? $quizScore->earned_points . '/' . $quizScore->total_points : '—' }}</td>
                                <td class="px-4 py-4 text-center font-bold {{ $quizScore->attempted ? 'text-brand-700' : 'text-slate-300' }}">{{ $quizScore->attempted && ! is_null($quizScore->percent) ? $quizScore->percent . '%' : '—' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <span class="badge {{ $sectionStatusCopy[$quizScore->status]['class'] }}">{{ $sectionStatusCopy[$quizScore->status]['label'] }}</span>
                                    @unless ($quizScore->attempted)
                                        <a href="{{ route('user.onboarding-assessment.index', ['quiz' => $quizScore->quiz->id]) }}" class="mt-1 block text-xs font-semibold text-brand-700 hover:text-brand-800">Take it now →</a>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr class="border-t-2 border-slate-200 bg-slate-50 font-bold text-slate-800">
                            <td class="px-6 py-4">Total</td>
                            <td class="px-4 py-4 text-center">{{ $totalQuestionsInAssessment }}</td>
                            <td class="px-4 py-4 text-center text-emerald-600">{{ $score->correct_count }}</td>
                            <td class="px-4 py-4 text-center {{ $totalIncorrect > 0 ? 'text-red-500' : '' }}">{{ $totalIncorrect }}</td>
                            <td class="px-4 py-4 text-center {{ $score->pending_count > 0 ? 'text-amber-500' : '' }}">{{ $score->pending_count > 0 ? $score->pending_count : '—' }}</td>
                            <td class="px-4 py-4 text-center">{{ $score->earned_points }}/{{ $score->full_total_points }}</td>
                            <td class="px-4 py-4 text-center text-brand-700">{{ ! is_null($score->percent) ? $score->percent . '%' : '—' }}</td>
                            <td class="px-6 py-4 text-right">
                                @if (isset($overallStatusCopy[$score->status]))
                                    <span class="badge {{ $overallStatusCopy[$score->status]['class'] }}">{{ $overallStatusCopy[$score->status]['label'] }}</span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if ($score->pending_count > 0)
                <div class="flex items-start gap-2 border-t border-slate-100 bg-amber-50 px-6 py-3 text-sm text-amber-700">
                    <x-icon name="help-circle" class="mt-0.5 h-4 w-4 shrink-0" />
                    <p>Text answers awaiting review are counted as "Pending" until an admin grades them — your final score may still change.</p>
                </div>
            @endif
        </div>
    @endif

</x-layout>
