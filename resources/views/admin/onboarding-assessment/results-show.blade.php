<x-layout title="Assessment Result">

    @php
        $questionTypeLabels = ['radio' => 'Single choice', 'checkbox' => 'Multiple choice', 'text' => 'Text answer'];
        $statusBadge = [
            'pending_review' => ['label' => 'Pending Review', 'class' => 'badge-amber'],
            'passed' => ['label' => 'Passed', 'class' => 'badge-green'],
            'failed' => ['label' => 'Failed', 'class' => 'bg-red-50 text-red-600'],
        ];
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
                    <span class="badge {{ $statusBadge[$score->status]['class'] }}">{{ $statusBadge[$score->status]['label'] }}</span>
                </div>
                <form method="POST" action="{{ route('admin.onboarding-assessment.results.retake', $student) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Allow {{ $student->name }} to retake the assessment? Their current answers and score will be cleared.', target: $el })">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                        <x-icon name="refresh-cw" class="h-4 w-4" />
                        Allow Retake
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-6 space-y-4">
        @foreach ($questions as $question)
            @php $answer = $answers->get($question->id); @endphp
            <div class="card p-5">
                <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $questionTypeLabels[$question->type] }} &middot; {{ $question->points }} {{ Str::plural('pt', $question->points) }}</span>
                <p class="mt-1 font-medium text-slate-800">{{ $question->question_text }}</p>

                @if (! $answer)
                    <p class="mt-2 text-sm italic text-slate-400">Not answered.</p>
                @elseif ($question->type === 'text')
                    <p class="mt-2 rounded-lg bg-slate-50 p-3 text-sm text-slate-600">{{ $answer->answer_text ?: '—' }}</p>

                    @if (is_null($answer->points_awarded))
                        <form method="POST" action="{{ route('admin.onboarding-assessment.answers.grade', $answer) }}" class="mt-3 flex items-end gap-3">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label class="form-label">Points (0–{{ $question->points }})</label>
                                <input type="number" name="points_awarded" min="0" max="{{ $question->points }}" required class="form-input w-32">
                            </div>
                            <button type="submit" class="btn-primary">Save Grade</button>
                        </form>
                    @else
                        <p class="mt-2 text-sm font-medium {{ $answer->is_correct ? 'text-emerald-600' : 'text-red-600' }}">
                            Graded: {{ $answer->points_awarded }}/{{ $question->points }} pts
                        </p>
                    @endif
                @else
                    <ul class="mt-2 space-y-1">
                        @foreach ($question->options as $option)
                            @php $wasSelected = in_array($option->id, $answer->selected_option_ids ?? []); @endphp
                            <li class="flex items-center gap-1.5 text-sm {{ $option->is_correct ? 'font-medium text-emerald-600' : ($wasSelected ? 'font-medium text-red-600' : 'text-slate-400') }}">
                                <x-icon name="{{ $wasSelected ? 'check-circle' : 'x' }}" class="h-3.5 w-3.5" />
                                {{ $option->option_text }}
                                @if ($wasSelected) <span class="text-xs">(selected)</span> @endif
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-2 text-sm font-medium {{ $answer->is_correct ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $answer->points_awarded }}/{{ $question->points }} pts
                    </p>
                @endif
            </div>
        @endforeach
    </div>

</x-layout>
