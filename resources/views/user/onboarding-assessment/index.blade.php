<x-layout title="Onboarding Assessment" subtitle="Test your knowledge and see how you score">

    @php
        $questionTypeLabels = ['radio' => 'Single choice', 'checkbox' => 'Multiple choice', 'text' => 'Text answer'];
        $overallStatusCopy = [
            'in_progress' => ['label' => 'In Progress', 'class' => 'badge-amber', 'note' => 'Complete every quiz below to get your final score.'],
            'pending_review' => ['label' => 'Pending Review', 'class' => 'badge-amber', 'note' => 'Some of your answers are being reviewed by an admin. Your final score will update once grading is complete.'],
            'passed' => ['label' => 'Passed', 'class' => 'badge-green', 'note' => null],
            'failed' => ['label' => 'Not Passed', 'class' => 'bg-red-50 text-red-600', 'note' => null],
        ];
        $quizStatusCopy = [
            'pending_review' => ['label' => 'Pending Review', 'class' => 'badge-amber', 'note' => 'Some of your answers are being reviewed by an admin.'],
            'graded' => ['label' => 'Completed', 'class' => 'badge-green', 'note' => null],
        ];
        $scoreByQuizId = collect($score->quizzes)->keyBy(fn ($q) => $q->quiz->id);

        $ringPercent = $score->percent ?? 0;

        $pointsNeeded = $score->attempted && ! is_null($score->percent) && $score->percent < $score->passing_score_percent
            ? max(0, $score->passing_score_percent - $score->percent)
            : null;

        $totalQuestionsInAssessment = $score->quizzes->sum('total_question_count');
        $quizzesRemaining = max(0, $score->quiz_count - $score->attempted_quiz_count);
        $firstRemainingQuiz = $quizzes->first(fn ($candidate) => ! ($scoreByQuizId->get($candidate->id)?->attempted));
    @endphp

    @if (! $settings->is_published)
        <div class="card p-10 text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                <x-icon name="lock" class="h-6 w-6" />
            </span>
            <p class="mt-4 font-bold text-slate-800">Assessment not available yet</p>
            <p class="mt-1 text-sm text-slate-400">Check back later — your admin hasn't published the onboarding assessment.</p>
        </div>
    @else
        <div class="card p-6">
            <div class="flex flex-wrap items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('favicon.png') }}" alt="Onboarding Assessment" class="h-28 w-28 shrink-0 object-contain sm:h-32 sm:w-32" />
                    <div>
                        <p class="font-bold text-slate-800">Onboarding Assessment</p>
                        <p class="mt-1 text-sm text-slate-400">{{ $score->quiz_count }} {{ Str::plural('Quiz', $score->quiz_count) }} Assigned</p>

                        @if ($score->attempted && isset($overallStatusCopy[$score->status]))
                            <span class="badge {{ $overallStatusCopy[$score->status]['class'] }} mt-3">{{ $overallStatusCopy[$score->status]['label'] }}</span>
                        @endif
                    </div>
                </div>

                @if ($score->attempted)
                    <div class="flex items-center gap-6">
                        @if (! is_null($score->percent))
                            <div class="flex h-24 w-24 shrink-0 flex-col items-center justify-center rounded-xl border-2 border-brand-700 bg-gradient-to-br from-brand-600 to-brand-700 p-2 text-center shadow-lg shadow-brand-200">
                                <span class="text-lg font-extrabold text-white">{{ $ringPercent }}%</span>
                                <span class="text-[10px] font-medium leading-tight text-brand-100">Score Achieved</span>
                            </div>

                            <div class="hidden h-16 w-px shrink-0 bg-slate-200 sm:block"></div>
                        @endif

                        <div class="space-y-3 text-right">
                            <div>
                                <p class="text-2xl font-extrabold text-brand-700">{{ $score->earned_points }}/{{ $score->full_total_points }} <span class="text-sm font-medium text-slate-400">pts</span></p>
                                <p class="flex items-center justify-end gap-1 text-xs text-slate-400">Score Earned (So Far)</p>
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold text-emerald-600">{{ $score->passing_score_percent }} <span class="text-sm font-medium text-slate-400">/ 100 %</span></p>
                                <p class="flex items-center justify-end gap-1 text-xs text-slate-400">
                                    Target Passing Score
                                    <x-icon name="help-circle" class="h-3 w-3" title="The minimum score needed to pass the onboarding assessment" />
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if ($score->attempted && ! is_null($score->percent))
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-amber-50 px-4 py-3">
                    <p class="flex items-center gap-2 text-sm text-amber-700">
                        <x-icon name="lightbulb" class="h-4 w-4 shrink-0" />
                        <span class="font-semibold">Score Achieved: {{ $ringPercent }}%</span>
                        You scored {{ $ringPercent }}% in {{ $score->attempted_quiz_count }} out of {{ $score->quiz_count }} {{ Str::plural('quiz', $score->quiz_count) }}.
                    </p>
                    <a href="{{ route('user.onboarding-assessment.results') }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-xl border border-amber-200 bg-white px-4 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-100">
                        View My Result
                        <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                    </a>
                </div>
            @endif

            @if (! $score->all_attempted)
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-brand-50 px-4 py-3">
                    <p class="flex items-center gap-2 text-sm text-brand-700">
                        <x-icon name="sparkles" class="h-4 w-4 shrink-0" />
                        Keep going! Complete the remaining quizzes to improve your score.
                    </p>
                    @if ($firstRemainingQuiz)
                        <a href="{{ route('user.onboarding-assessment.index', ['quiz' => $firstRemainingQuiz->id]) }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-xl border border-brand-200 bg-white px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-100">
                            {{ $quizzesRemaining }} {{ Str::plural('quiz', $quizzesRemaining) }} remaining to complete
                            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                        </a>
                    @else
                        <p class="text-sm font-semibold text-brand-700">{{ $quizzesRemaining }} {{ Str::plural('quiz', $quizzesRemaining) }} remaining to complete</p>
                    @endif
                </div>
            @endif

            <div class="mt-6 grid grid-cols-5 gap-2 border-t border-slate-100 pt-6 sm:gap-3">
                <div class="rounded-xl border border-slate-100 p-2 text-center sm:p-4">
                    <span class="mx-auto flex h-7 w-7 items-center justify-center rounded-full bg-brand-50 text-brand-600 sm:h-9 sm:w-9">
                        <x-icon name="document" class="h-3.5 w-3.5 sm:h-4 sm:w-4" />
                    </span>
                    <p class="mt-2 text-sm font-extrabold text-slate-800 sm:text-lg">{{ $score->quiz_count }}</p>
                    <p class="text-[10px] leading-tight text-slate-400 sm:text-xs">Total Quizzes</p>
                </div>
                <div class="rounded-xl border border-slate-100 p-2 text-center sm:p-4">
                    <span class="mx-auto flex h-7 w-7 items-center justify-center rounded-full bg-sky-50 text-sky-600 sm:h-9 sm:w-9">
                        <x-icon name="help-circle" class="h-3.5 w-3.5 sm:h-4 sm:w-4" />
                    </span>
                    <p class="mt-2 text-sm font-extrabold text-slate-800 sm:text-lg">{{ $totalQuestionsInAssessment }}</p>
                    <p class="text-[10px] leading-tight text-slate-400 sm:text-xs">Total Questions</p>
                </div>
                <div class="rounded-xl border border-slate-100 p-2 text-center sm:p-4">
                    <span class="mx-auto flex h-7 w-7 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 sm:h-9 sm:w-9">
                        <x-icon name="check-circle" class="h-3.5 w-3.5 sm:h-4 sm:w-4" />
                    </span>
                    <p class="mt-2 text-sm font-extrabold text-slate-800 sm:text-lg">{{ $score->attempted_quiz_count }}</p>
                    <p class="text-[10px] leading-tight text-slate-400 sm:text-xs">Quizzes Completed</p>
                </div>
                @php $remainingTag = $firstRemainingQuiz ? 'a' : 'div'; @endphp
                <{{ $remainingTag }}
                    @if ($firstRemainingQuiz) href="{{ route('user.onboarding-assessment.index', ['quiz' => $firstRemainingQuiz->id]) }}" title="Go to your next remaining quiz" @endif
                    class="rounded-xl border border-slate-100 p-2 text-center transition sm:p-4 {{ $firstRemainingQuiz ? 'cursor-pointer hover:border-brand-200 hover:bg-brand-50/40' : '' }}"
                >
                    <span class="mx-auto flex h-7 w-7 items-center justify-center rounded-full bg-red-50 text-red-500 sm:h-9 sm:w-9">
                        <x-icon name="x" class="h-3.5 w-3.5 sm:h-4 sm:w-4" />
                    </span>
                    <p class="mt-2 text-sm font-extrabold text-slate-800 sm:text-lg">{{ $quizzesRemaining }}</p>
                    <p class="text-[10px] leading-tight text-slate-400 sm:text-xs">Quizzes Remaining</p>
                </{{ $remainingTag }}>
                <div class="rounded-xl border border-slate-100 p-2 text-center sm:p-4">
                    <span class="mx-auto flex h-7 w-7 items-center justify-center rounded-full bg-amber-50 text-amber-600 sm:h-9 sm:w-9">
                        <x-icon name="badge" class="h-3.5 w-3.5 sm:h-4 sm:w-4" />
                    </span>
                    <p class="mt-2 text-sm font-extrabold text-slate-800 sm:text-lg">{{ $score->passing_score_percent }} <span class="text-[10px] font-medium text-slate-400 sm:text-xs">/100 %</span></p>
                    <p class="text-[10px] leading-tight text-slate-400 sm:text-xs">Target Passing Score</p>
                </div>
            </div>
        </div>

        @if ($quizzes->isEmpty())
            <div class="card mt-6 p-8 text-center text-sm text-slate-400">No quizzes have been added yet.</div>
        @elseif ($score->all_attempted)
            <div class="card mt-6 p-10 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50">
                    <x-icon name="check-circle" class="h-8 w-8 text-emerald-600" />
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-800">Assessment Completed!</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">
                    @if ($score->status === 'pending_review')
                        You've answered every quiz. A few of your answers are still being reviewed — your final score will update once grading is complete.
                    @else
                        You've answered every quiz in the onboarding assessment. Great work!
                    @endif
                </p>

                <div class="mt-6 grid grid-cols-1 gap-3 text-left sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($score->quizzes as $quizScoreRow)
                        <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 px-4 py-3">
                            <p class="min-w-0 truncate text-sm font-medium text-slate-700">{{ $quizScoreRow->quiz->title }}</p>
                            <div class="flex shrink-0 items-center gap-3">
                                <span class="text-sm font-bold text-brand-700">
                                    {{ $quizScoreRow->earned_points }}/{{ $quizScoreRow->total_points }} pts
                                    @if (! is_null($quizScoreRow->percent))
                                        <span class="font-medium text-slate-400">({{ $quizScoreRow->percent }}%)</span>
                                    @endif
                                </span>
                                <span class="badge {{ $quizStatusCopy[$quizScoreRow->status]['class'] }}">{{ $quizStatusCopy[$quizScoreRow->status]['label'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <a href="{{ route('user.onboarding-assessment.results') }}" class="mt-5 inline-flex items-center gap-1.5 btn-primary">
                    View My Results
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </a>
            </div>
        @else
            <div class="mt-6 flex items-center gap-2 overflow-x-auto rounded-full bg-slate-100 p-1.5">
                @foreach ($quizzes as $quiz)
                    @php
                        $tabQuizScore = $scoreByQuizId->get($quiz->id);
                        $tabActive = $activeQuiz?->id === $quiz->id;
                        $tabAttempted = $tabQuizScore && $tabQuizScore->attempted;
                    @endphp
                    <a
                        href="{{ route('user.onboarding-assessment.index', ['quiz' => $quiz->id]) }}"
                        class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium transition {{ $tabActive ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}"
                    >
                        <x-icon name="{{ $tabAttempted ? 'check-circle' : 'document' }}" class="h-4 w-4 {{ $tabAttempted && ! $tabActive ? 'text-emerald-500' : '' }}" />
                        {{ $quiz->title }}
                    </a>
                @endforeach
            </div>

            @php
                $quiz = $activeQuiz;
                $quizScore = $scoreByQuizId->get($quiz->id);
                $nextQuiz = $quizzes->first(fn ($candidate) => $candidate->id !== $quiz->id && ! ($scoreByQuizId->get($candidate->id)?->attempted));
            @endphp
            <div class="mt-6 space-y-6">
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
                                    <span class="text-xs text-slate-400">{{ $quiz->questions->count() }} {{ Str::plural('question', $quiz->questions->count()) }} &middot; {{ $quiz->questions->sum('points') }} pts</span>
                                @endif
                            </div>
                        </div>

                        @if ($quizScore && $quizScore->attempted)
                            {{-- Already submitted — read-only review --}}
                            <div class="space-y-4 p-6">
                                @if ($quizStatusCopy[$quizScore->status]['note'])
                                    <div class="flex items-start gap-2 rounded-xl bg-brand-50 px-4 py-3 text-sm text-brand-700">
                                        <x-icon name="help-circle" class="mt-0.5 h-4 w-4 shrink-0" />
                                        <p>{{ $quizStatusCopy[$quizScore->status]['note'] }}</p>
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
                                                            @if ($option->is_correct)
                                                                <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                                            @elseif ($wasSelected)
                                                                <x-icon name="x" class="h-3.5 w-3.5" />
                                                            @else
                                                                <span class="h-3.5 w-3.5"></span>
                                                            @endif
                                                            {{ $option->option_text }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                @php
                                                    $awarded = $answer->points_awarded ?? 0;
                                                    $pointsColor = $awarded >= $questionPoints ? 'text-emerald-600' : ($awarded > 0 ? 'text-amber-600' : 'text-red-600');
                                                @endphp
                                                <p class="mt-2 text-sm font-medium {{ $pointsColor }}">
                                                    {{ $awarded }}/{{ $questionPoints }} pts
                                                    @if ($awarded > 0 && $awarded < $questionPoints)
                                                        <span class="font-normal text-slate-400">(partial credit)</span>
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                @if ($nextQuiz)
                                    <a href="{{ route('user.onboarding-assessment.index', ['quiz' => $nextQuiz->id]) }}" class="btn-primary inline-flex w-full items-center justify-center gap-1.5 sm:w-auto">
                                        Next Quiz
                                        <x-icon name="chevron-right" class="h-4 w-4" />
                                    </a>
                                @endif
                            </div>
                        @elseif ($quiz->questions->isEmpty())
                            <div class="p-6 text-center text-sm text-slate-400">This quiz has no questions yet.</div>
                        @else
                            {{-- Not yet attempted — fillable form --}}
                            <form method="POST" action="{{ route('user.onboarding-assessment.submit', $quiz) }}" class="space-y-4 p-6" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Submit your answers for \'{{ $quiz->title }}\'? You won\'t be able to retake it yourself.', target: $el })">
                                @csrf
                                <p class="text-xs font-medium text-amber-600">You get one attempt at this quiz — answers are final once submitted.</p>

                                @foreach ($quiz->questions as $question)
                                    <div class="flex gap-3 rounded-xl border border-slate-100 p-4">
                                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-700">{{ $loop->iteration }}</span>
                                        <div class="min-w-0 flex-1">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-brand-500">{{ $questionTypeLabels[$question->type] }} &middot; {{ $question->points }} {{ Str::plural('pt', $question->points) }}</span>
                                            <p class="mt-1 font-medium text-slate-800">{{ $question->question_text }}</p>

                                            <div class="mt-3 space-y-2">
                                                @if ($question->type === 'text')
                                                    <textarea name="answers[{{ $question->id }}][text]" rows="3" class="form-input" placeholder="Type your answer..."></textarea>
                                                @else
                                                    @foreach ($question->options as $option)
                                                        <label class="flex items-center gap-2 text-sm text-slate-700">
                                                            <input type="{{ $question->type === 'radio' ? 'radio' : 'checkbox' }}" name="answers[{{ $question->id }}][selected][]" value="{{ $option->id }}" class="{{ $question->type === 'radio' ? '' : 'rounded' }} border-slate-300 text-brand-600 focus:ring-brand-500">
                                                            {{ $option->option_text }}
                                                        </label>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <button type="submit" class="btn-primary w-full sm:w-auto">Submit Quiz</button>
                            </form>
                        @endif
                    </div>
            </div>
        @endif
    @endif

</x-layout>
