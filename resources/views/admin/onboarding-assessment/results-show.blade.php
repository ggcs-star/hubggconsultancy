<x-layout title="Assessment Result">

    @php
        $questionTypeLabels = ['radio' => 'Single choice', 'checkbox' => 'Multiple choice', 'text' => 'Text answer'];
        $statusBadge = [
            'not_started' => ['label' => 'Not Started', 'class' => 'badge-slate'],
            'in_progress' => ['label' => 'In Progress', 'class' => 'badge-amber'],
            'pending_review' => ['label' => 'Pending Review', 'class' => 'badge-amber'],
            'passed' => ['label' => 'Passed', 'class' => 'badge-green'],
            'failed' => ['label' => 'Failed', 'class' => 'bg-red-50 text-red-600'],
        ];
        $quizStatusBadge = [
            'not_started' => ['label' => 'Not Started', 'class' => 'badge-slate'],
            'pending_review' => ['label' => 'Pending Review', 'class' => 'badge-amber'],
            'graded' => ['label' => 'Graded', 'class' => 'badge-green'],
        ];
        $scoreByQuizId = collect($score->quizzes)->keyBy(fn ($q) => $q->quiz->id);
    @endphp

    <a href="{{ route('admin.onboarding-assessment.index', ['tab' => 'results']) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
        <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
        Back to Results
    </a>

    <div class="card mt-4 p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-700 text-lg font-bold text-white">
                    {{ strtoupper(substr($student->name, 0, 1)) }}
                </span>
                <div>
                    <p class="font-bold text-slate-800">{{ $student->name }}</p>
                    <p class="text-sm text-slate-400">{{ $student->email }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-2xl font-extrabold text-slate-800">{{ $score->earned_points }}/{{ $score->total_points }} <span class="text-base font-medium text-slate-400">pts</span></p>
                    @if (! is_null($score->percent))
                        <p class="text-xs text-slate-400">{{ $score->percent }}% &middot; Passing score: {{ $score->passing_score_percent }}%</p>
                    @endif
                    <span class="badge {{ $statusBadge[$score->status]['class'] }}">{{ $statusBadge[$score->status]['label'] }}</span>
                    <p class="mt-1 text-xs text-slate-400">{{ $score->attempted_quiz_count }}/{{ $score->quiz_count }} {{ Str::plural('quiz', $score->quiz_count) }} completed</p>
                </div>
                @if ($score->attempted)
                    <form method="POST" action="{{ route('admin.onboarding-assessment.results.retake', $student) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Allow {{ $student->name }} to retake every quiz? Their current answers and score will be cleared.', target: $el })">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                            <x-icon name="refresh-cw" class="h-4 w-4" />
                            Retake All
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-6 space-y-6">
        @forelse ($quizzes as $quiz)
            @php $quizScore = $scoreByQuizId->get($quiz->id); @endphp
            <div class="card">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="font-bold text-slate-800">{{ $quiz->title }}</p>
                            <span class="badge {{ $quiz->is_published ? 'badge-green' : 'badge-slate' }}">{{ $quiz->is_published ? 'Published' : 'Draft' }}</span>
                        </div>
                        @if ($quizScore && $quizScore->attempted)
                            <p class="text-sm text-slate-400">
                                {{ $quizScore->earned_points }}/{{ $quizScore->total_points }} pts
                                @if (! is_null($quizScore->percent))
                                    &middot; {{ $quizScore->percent }}%
                                @endif
                            </p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        @if ($quizScore)
                            <span class="badge {{ $quizStatusBadge[$quizScore->status]['class'] }}">{{ $quizStatusBadge[$quizScore->status]['label'] }}</span>
                        @endif
                        @if ($quizScore && $quizScore->attempted && ! $quiz->trashed())
                            <form method="POST" action="{{ route('admin.onboarding-assessment.results.retake-quiz', [$student, $quiz]) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Allow {{ $student->name }} to retake \'{{ $quiz->title }}\'? Their current answers for this quiz will be cleared.', target: $el })">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Retake</button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($quiz->questions as $question)
                        @php
                            $answer = $answers->get($question->id);
                            $questionPoints = $answer?->question_points ?? $question->points;
                            $questionText = $answer?->question_text ?? $question->question_text;
                        @endphp
                        <div class="p-5">
                            <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $questionTypeLabels[$question->type] }} &middot; {{ $questionPoints }} {{ Str::plural('pt', $questionPoints) }}</span>
                            <p class="mt-1 font-medium text-slate-800">{{ $questionText }}</p>

                            @if (! $answer)
                                <p class="mt-2 text-sm italic text-slate-400">Not answered.</p>
                            @elseif ($question->type === 'text')
                                <p class="mt-2 rounded-lg bg-slate-50 p-3 text-sm text-slate-600">{{ $answer->answer_text ?: '—' }}</p>

                                @if (is_null($answer->points_awarded))
                                    <form method="POST" action="{{ route('admin.onboarding-assessment.answers.grade', $answer) }}" class="mt-3 flex items-end gap-3">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label class="form-label">Points (0–{{ $questionPoints }})</label>
                                            <input type="number" name="points_awarded" min="0" max="{{ $questionPoints }}" required class="form-input w-32">
                                        </div>
                                        <button type="submit" class="btn-primary">Save Grade</button>
                                    </form>
                                @else
                                    <p class="mt-2 text-sm font-medium {{ $answer->is_correct ? 'text-emerald-600' : 'text-red-600' }}">
                                        Graded: {{ $answer->points_awarded }}/{{ $questionPoints }} pts
                                    </p>
                                @endif
                            @else
                                <ul class="mt-2 space-y-1">
                                    @foreach ($question->options as $option)
                                        @php $wasSelected = $option->selected ?? in_array($option->id, $answer->selected_option_ids ?? []); @endphp
                                        <li class="flex items-center gap-1.5 text-sm {{ $option->is_correct ? 'font-medium text-emerald-600' : ($wasSelected ? 'font-medium text-red-600' : 'text-slate-400') }}">
                                            <x-icon name="{{ $wasSelected ? 'check-circle' : 'x' }}" class="h-3.5 w-3.5" />
                                            {{ $option->option_text }}
                                            @if ($wasSelected) <span class="text-xs">(selected)</span> @endif
                                        </li>
                                    @endforeach
                                </ul>
                                <p class="mt-2 text-sm font-medium {{ $answer->is_correct ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $answer->points_awarded }}/{{ $questionPoints }} pts
                                </p>
                            @endif
                        </div>
                    @empty
                        <p class="px-5 py-6 text-center text-sm text-slate-400">No questions in this quiz.</p>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="card p-8 text-center text-sm text-slate-400">No quizzes have been added yet.</div>
        @endforelse
    </div>

</x-layout>
