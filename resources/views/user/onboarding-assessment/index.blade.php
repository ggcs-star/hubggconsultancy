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
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="font-bold text-slate-800">Onboarding Assessment</p>
                    <p class="mt-2 text-sm text-slate-400">{{ $score->attempted_quiz_count }}/{{ $score->quiz_count }} {{ Str::plural('quiz', $score->quiz_count) }} completed</p>
                </div>
                @if ($score->attempted)
                    <div class="text-right">
                        <p class="text-3xl font-extrabold text-slate-800">{{ $score->earned_points }}/{{ $score->total_points }} <span class="text-base font-medium text-slate-400">pts</span></p>
                        @if (! is_null($score->percent))
                            <p class="text-sm text-slate-400">{{ $score->percent }}% &middot; Passing score: {{ $score->passing_score_percent }}%</p>
                        @endif
                    </div>
                @endif
            </div>

            @if ($score->attempted && isset($overallStatusCopy[$score->status]))
                <div class="mt-4 flex items-center gap-3 border-t border-slate-100 pt-4">
                    <span class="badge {{ $overallStatusCopy[$score->status]['class'] }}">{{ $overallStatusCopy[$score->status]['label'] }}</span>
                    @if ($overallStatusCopy[$score->status]['note'])
                        <p class="text-sm text-slate-500">{{ $overallStatusCopy[$score->status]['note'] }}</p>
                    @endif
                </div>
            @endif
        </div>

        @if ($quizzes->isEmpty())
            <div class="card mt-6 p-8 text-center text-sm text-slate-400">No quizzes have been added yet.</div>
        @else
            <div class="mt-6 flex items-center gap-6 overflow-x-auto border-b border-slate-200">
                @foreach ($quizzes as $quiz)
                    @php $tabQuizScore = $scoreByQuizId->get($quiz->id); @endphp
                    <x-tab-link
                        :href="route('user.onboarding-assessment.index', ['quiz' => $quiz->id])"
                        :active="$activeQuiz?->id === $quiz->id"
                        :icon="$tabQuizScore && $tabQuizScore->attempted ? 'check-circle' : null"
                    >
                        {{ $quiz->title }}
                    </x-tab-link>
                @endforeach
            </div>

            @php $quiz = $activeQuiz; $quizScore = $scoreByQuizId->get($quiz->id); @endphp
            <div class="mt-6 space-y-6">
                    <div class="card">
                        <div class="border-b border-slate-100 px-6 py-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="font-bold text-slate-800">{{ $quiz->title }}</p>
                                    @if ($quiz->description)
                                        <p class="mt-0.5 text-sm text-slate-400">{{ $quiz->description }}</p>
                                    @endif
                                </div>
                                @if ($quizScore && $quizScore->attempted)
                                    <div class="text-right">
                                        <p class="font-bold text-slate-800">{{ $quizScore->earned_points }}/{{ $quizScore->total_points }} <span class="text-sm font-medium text-slate-400">pts</span></p>
                                        <span class="badge {{ $quizStatusCopy[$quizScore->status]['class'] }}">{{ $quizStatusCopy[$quizScore->status]['label'] }}</span>
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
                                    <p class="text-sm text-slate-500">{{ $quizStatusCopy[$quizScore->status]['note'] }}</p>
                                @endif
                                @foreach ($quiz->questions as $question)
                                    @php $answer = $answers->get($question->id); @endphp
                                    <div class="rounded-xl border border-slate-100 p-4">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $questionTypeLabels[$question->type] }} &middot; {{ $question->points }} {{ Str::plural('pt', $question->points) }}</span>
                                        <p class="mt-1 font-medium text-slate-800">{{ $question->question_text }}</p>

                                        @if ($question->type === 'text')
                                            <p class="mt-2 rounded-lg bg-slate-50 p-3 text-sm text-slate-600">{{ $answer?->answer_text ?: '—' }}</p>
                                            <p class="mt-2 text-xs italic text-slate-400">
                                                {{ is_null($answer?->points_awarded) ? 'Awaiting review' : $answer->points_awarded . '/' . $question->points . ' pts' }}
                                            </p>
                                        @else
                                            <ul class="mt-2 space-y-1">
                                                @foreach ($question->options as $option)
                                                    @php $wasSelected = in_array($option->id, $answer->selected_option_ids ?? []); @endphp
                                                    <li class="flex items-center gap-1.5 text-sm {{ $option->is_correct ? 'font-medium text-emerald-600' : ($wasSelected ? 'font-medium text-red-600' : 'text-slate-400') }}">
                                                        <x-icon name="{{ $wasSelected ? 'check-circle' : 'x' }}" class="h-3.5 w-3.5" />
                                                        {{ $option->option_text }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <p class="mt-2 text-sm font-medium {{ $answer?->is_correct ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ $answer->points_awarded ?? 0 }}/{{ $question->points }} pts
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @elseif ($quiz->questions->isEmpty())
                            <div class="p-6 text-center text-sm text-slate-400">This quiz has no questions yet.</div>
                        @else
                            {{-- Not yet attempted — fillable form --}}
                            <form method="POST" action="{{ route('user.onboarding-assessment.submit', $quiz) }}" class="space-y-4 p-6" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Submit your answers for \'{{ $quiz->title }}\'? You won\'t be able to retake it yourself.', target: $el })">
                                @csrf
                                <p class="text-xs font-medium text-amber-600">You get one attempt at this quiz — answers are final once submitted.</p>

                                @foreach ($quiz->questions as $question)
                                    <div class="rounded-xl border border-slate-100 p-4">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $questionTypeLabels[$question->type] }} &middot; {{ $question->points }} {{ Str::plural('pt', $question->points) }}</span>
                                        <p class="mt-1 font-medium text-slate-800">{{ $loop->iteration }}. {{ $question->question_text }}</p>

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
                                @endforeach

                                <button type="submit" class="btn-primary w-full sm:w-auto">Submit Quiz</button>
                            </form>
                        @endif
                    </div>
            </div>
        @endif
    @endif

</x-layout>
