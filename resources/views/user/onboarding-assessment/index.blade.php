<x-layout title="Onboarding Assessment" subtitle="Test your knowledge and see how you score">

    @php
        $questionTypeLabels = ['radio' => 'Single choice', 'checkbox' => 'Multiple choice', 'text' => 'Text answer'];
        $statusCopy = [
            'pending_review' => ['label' => 'Pending Review', 'class' => 'badge-amber', 'note' => 'Some of your answers are being reviewed by an admin. Your final score will update once grading is complete.'],
            'passed' => ['label' => 'Passed', 'class' => 'badge-green', 'note' => null],
            'failed' => ['label' => 'Not Passed', 'class' => 'bg-red-50 text-red-600', 'note' => null],
        ];
    @endphp

    @if (! $settings->is_published)
        <div class="card p-10 text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                <x-icon name="lock" class="h-6 w-6" />
            </span>
            <p class="mt-4 font-bold text-slate-800">Assessment not available yet</p>
            <p class="mt-1 text-sm text-slate-400">Check back later — your admin hasn't published the onboarding assessment.</p>
        </div>
    @elseif ($score->attempted)
        <div class="card p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="font-bold text-slate-800">{{ $settings->title }}</p>
                    <p class="text-sm text-slate-400">Submitted {{ $score->submitted_at?->format('d M Y, h:i A') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-extrabold text-slate-800">{{ $score->earned_points }}/{{ $score->total_points }} <span class="text-base font-medium text-slate-400">pts</span></p>
                    @if (! is_null($score->percent))
                        <p class="text-sm text-slate-400">{{ $score->percent }}% &middot; Passing score: {{ $score->passing_score_percent }}%</p>
                    @endif
                </div>
            </div>

            <div class="mt-4 flex items-center gap-3 border-t border-slate-100 pt-4">
                <span class="badge {{ $statusCopy[$score->status]['class'] }}">{{ $statusCopy[$score->status]['label'] }}</span>
                @if ($statusCopy[$score->status]['note'])
                    <p class="text-sm text-slate-500">{{ $statusCopy[$score->status]['note'] }}</p>
                @endif
            </div>
        </div>

        <div class="mt-6 space-y-4">
            @foreach ($questions as $question)
                @php $answer = $answers->get($question->id); @endphp
                <div class="card p-5">
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
    @else
        <div class="card p-6">
            <p class="font-bold text-slate-800">{{ $settings->title }}</p>
            @if ($settings->description)
                <p class="mt-1 text-sm text-slate-500">{{ $settings->description }}</p>
            @endif
            <div class="mt-4 flex flex-wrap gap-4 text-sm text-slate-500">
                <span>{{ $questions->count() }} questions</span>
                <span>{{ $questions->sum('points') }} total points</span>
                <span>Passing score: {{ $settings->passing_score_percent }}%</span>
            </div>
            <p class="mt-3 text-xs font-medium text-amber-600">You get one attempt — answers are final once submitted.</p>
        </div>

        @if ($questions->isEmpty())
            <div class="card mt-6 p-8 text-center text-sm text-slate-400">No questions have been added yet.</div>
        @else
            <form method="POST" action="{{ route('user.onboarding-assessment.submit') }}" class="mt-6 space-y-4" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Submit your answers? You won\'t be able to retake this assessment.', target: $el })">
                @csrf

                @foreach ($questions as $question)
                    <div class="card p-5">
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

                <button type="submit" class="btn-primary w-full sm:w-auto">Submit Assessment</button>
            </form>
        @endif
    @endif

</x-layout>
