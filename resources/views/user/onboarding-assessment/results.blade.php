<x-layout title="My Results" subtitle="Your onboarding assessment score and answer breakdown">

    @php
        $questionTypeLabels = ['radio' => 'Single choice', 'checkbox' => 'Multiple choice', 'text' => 'Text answer'];
        $overallStatusCopy = [
            'in_progress' => ['label' => 'In Progress', 'class' => 'badge-amber', 'note' => 'Complete every quiz on the Assessments page to get your final score.'],
            'pending_review' => ['label' => 'Pending Review', 'class' => 'badge-amber', 'note' => 'Some of your answers are being reviewed by an admin. Your final score will update once grading is complete.'],
            'passed' => ['label' => 'Passed', 'class' => 'badge-green', 'note' => null],
            'failed' => ['label' => 'Not Passed', 'class' => 'bg-red-50 text-red-600', 'note' => null],
        ];
        $quizStatusCopy = [
            'not_started' => ['label' => 'Not Attempted', 'class' => 'badge-slate'],
            'pending_review' => ['label' => 'Pending Review', 'class' => 'badge-amber'],
            'graded' => ['label' => 'Completed', 'class' => 'badge-green'],
        ];
        $scoreByQuizId = collect($score->quizzes)->keyBy(fn ($q) => $q->quiz->id);

        $ringPercent = $score->percent ?? 0;
        $ringRadius = 45;
        $ringCircumference = 2 * M_PI * $ringRadius;
        $ringOffset = $ringCircumference * (1 - min(max($ringPercent, 0), 100) / 100);
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
                    <img src="{{ asset('images/documents.png') }}" alt="Onboarding Assessment" class="h-28 w-28 shrink-0 object-contain sm:h-32 sm:w-32" />
                    <div>
                        <p class="font-bold text-slate-800">Onboarding Assessment</p>
                        <p class="mt-1 text-sm text-slate-400">{{ $score->attempted_quiz_count }}/{{ $score->quiz_count }} {{ Str::plural('quiz', $score->quiz_count) }} completed</p>

                        @if (isset($overallStatusCopy[$score->status]))
                            <span class="badge {{ $overallStatusCopy[$score->status]['class'] }} mt-3">{{ $overallStatusCopy[$score->status]['label'] }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    @if (! is_null($score->percent))
                        <div class="relative flex h-24 w-24 shrink-0 items-center justify-center">
                            <svg class="h-24 w-24 -rotate-90" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="{{ $ringRadius }}" fill="none" stroke="#ede9fe" stroke-width="10" />
                                <circle
                                    cx="50" cy="50" r="{{ $ringRadius }}" fill="none" stroke="#7c3aed" stroke-width="10"
                                    stroke-linecap="round"
                                    stroke-dasharray="{{ $ringCircumference }}"
                                    stroke-dashoffset="{{ $ringOffset }}"
                                />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-lg font-extrabold text-slate-800">{{ $ringPercent }}%</span>
                                <span class="text-[10px] font-medium text-slate-400">Achieved Score</span>
                            </div>
                        </div>

                        <div class="hidden h-16 w-px shrink-0 bg-slate-200 sm:block"></div>
                    @endif

                    <div class="space-y-3 text-right">
                        <div>
                            <p class="text-2xl font-extrabold text-brand-700">{{ $score->earned_points }}/{{ $score->total_points }} <span class="text-sm font-medium text-slate-400">pts</span></p>
                            <p class="flex items-center justify-end gap-1 text-xs text-slate-400">Score Earned (So Far)</p>
                        </div>
                        @if (! is_null($score->percent))
                            <div>
                                <p class="text-2xl font-extrabold text-emerald-600">{{ $score->passing_score_percent }}%</p>
                                <p class="flex items-center justify-end gap-1 text-xs text-slate-400">Passing Score</p>
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

            @if (! is_null($score->percent))
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-brand-50 px-4 py-3">
                    <p class="flex items-center gap-2 text-sm text-brand-700">
                        <x-icon name="lightbulb" class="h-4 w-4 shrink-0" />
                        You have earned {{ $score->earned_points }} out of {{ $score->total_points }} points from completed quizzes.
                    </p>
                </div>
            @endif
        </div>

        <div class="mt-6 flex items-center gap-2 overflow-x-auto rounded-full bg-slate-100 p-1.5">
            @foreach ($quizzes as $quiz)
                @php
                    $tabQuizScore = $scoreByQuizId->get($quiz->id);
                    $tabActive = $activeQuiz?->id === $quiz->id;
                    $tabAttempted = $tabQuizScore && $tabQuizScore->attempted;
                @endphp
                <a
                    href="{{ route('user.onboarding-assessment.results', ['quiz' => $quiz->id]) }}"
                    class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium transition {{ $tabActive ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}"
                >
                    <x-icon name="{{ $tabAttempted ? 'check-circle' : 'document' }}" class="h-4 w-4 {{ $tabAttempted && ! $tabActive ? 'text-emerald-500' : '' }}" />
                    {{ $quiz->title }}
                </a>
            @endforeach
        </div>

        @php $quiz = $activeQuiz; $quizScore = $scoreByQuizId->get($quiz->id); @endphp
        <div class="mt-6">
            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                <x-icon name="document" class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="font-bold text-slate-800">{{ $quiz->title }}</p>
                                @if ($quiz->description)
                                    <p class="mt-0.5 text-sm text-slate-400">{{ $quiz->description }}</p>
                                @endif
                            </div>
                        </div>
                        @if ($quizScore && $quizScore->attempted)
                            <div class="text-right">
                                <p class="font-bold text-brand-700">{{ $quizScore->earned_points }}/{{ $quizScore->total_points }} <span class="text-sm font-medium text-slate-400">pts</span></p>
                                <span class="badge {{ $quizStatusCopy[$quizScore->status]['class'] }} mt-1">{{ $quizStatusCopy[$quizScore->status]['label'] }}</span>
                            </div>
                        @else
                            <span class="badge {{ $quizStatusCopy['not_started']['class'] }}">{{ $quizStatusCopy['not_started']['label'] }}</span>
                        @endif
                    </div>
                </div>

                @if ($quizScore && $quizScore->attempted)
                    <div class="space-y-4 p-6">
                        @if ($quizScore->status === 'pending_review')
                            <div class="flex items-start gap-2 rounded-xl bg-brand-50 px-4 py-3 text-sm text-brand-700">
                                <x-icon name="help-circle" class="mt-0.5 h-4 w-4 shrink-0" />
                                <p>Some of your answers are being reviewed by an admin.</p>
                            </div>
                        @endif
                        @foreach ($quiz->questions as $question)
                            @php
                                $answer = $answers->get($question->id);
                                $questionPoints = $answer?->question_points ?? $question->points;
                                $questionText = $answer?->question_text ?? $question->question_text;
                            @endphp
                            <div class="flex gap-3 rounded-xl border border-slate-100 p-4">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-700">{{ $loop->iteration }}</span>
                                <div class="min-w-0 flex-1">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-brand-500">{{ $questionTypeLabels[$question->type] }} &middot; {{ $questionPoints }} {{ Str::plural('pt', $questionPoints) }}</span>
                                    <p class="mt-1 font-medium text-slate-800">{{ $questionText }}</p>

                                    @if ($question->type === 'text')
                                        <p class="mt-2 rounded-lg bg-slate-50 p-3 text-sm text-slate-600">{{ $answer?->answer_text ?: '—' }}</p>
                                        <p class="mt-2 text-xs italic text-slate-400">
                                            {{ is_null($answer?->points_awarded) ? 'Awaiting review' : $answer->points_awarded . '/' . $questionPoints . ' pts' }}
                                        </p>
                                    @else
                                        <ul class="mt-2 space-y-1">
                                            @foreach ($question->options as $option)
                                                @php $wasSelected = $option->selected ?? in_array($option->id, $answer->selected_option_ids ?? []); @endphp
                                                <li class="flex items-center gap-1.5 text-sm {{ $option->is_correct ? 'font-medium text-emerald-600' : ($wasSelected ? 'font-medium text-red-600' : 'text-slate-400') }}">
                                                    <x-icon name="{{ $wasSelected ? 'check-circle' : 'x' }}" class="h-3.5 w-3.5" />
                                                    {{ $option->option_text }}
                                                </li>
                                            @endforeach
                                        </ul>
                                        <p class="mt-2 text-sm font-medium {{ $answer?->is_correct ? 'text-emerald-600' : 'text-red-600' }}">
                                            {{ $answer->points_awarded ?? 0 }}/{{ $questionPoints }} pts
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-10 text-center text-sm text-slate-400">
                        You haven't attempted this quiz yet.
                        <a href="{{ route('user.onboarding-assessment.index', ['quiz' => $quiz->id]) }}" class="ml-1 font-semibold text-brand-700 hover:text-brand-800">Take it now →</a>
                    </div>
                @endif
            </div>
        </div>
    @endif

</x-layout>
