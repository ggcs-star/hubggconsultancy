<x-layout title="Pending Quiz Reviews">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.courses.index') }}" class="hover:text-primary">Courses</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">Pending Reviews</span>
    </nav>

    <div class="mt-3">
        <h1 class="text-2xl font-semibold text-secondary-dark">Pending Quiz Reviews</h1>
        <p class="mt-1 text-sm text-secondary">Text-answer questions must be graded manually — this feeds directly into each client's course score.</p>
    </div>

    <div class="mt-6 space-y-4">
        @forelse ($pendingAnswers as $answer)
            @php
                $checkpoint = $answer->question->checkpoint;
                $course = $checkpoint->course;
                $context = $checkpoint->isModuleQuiz() ? 'Module Quiz: ' . $checkpoint->title : $checkpoint->lesson->title;
            @endphp
            <div class="rounded-xl border border-app-border bg-white p-5">
                <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-secondary">
                    <span>
                        <span class="font-medium text-secondary-dark">{{ $answer->user?->name }}</span>
                        — {{ $course->title }} / {{ $context }}
                    </span>
                    <span>{{ $answer->created_at->diffForHumans() }}</span>
                </div>

                <p class="mt-3 text-sm font-medium text-secondary-dark">
                    {{ $answer->question->question_text }}
                    <span class="font-normal text-xs text-secondary">({{ $answer->question->points }} {{ Str::plural('pt', $answer->question->points) }})</span>
                </p>
                <div class="mt-2 rounded-lg bg-surface-alt p-3 text-sm text-secondary-dark">
                    {{ $answer->answer_text }}
                </div>

                <form method="POST" action="{{ route('admin.course-quiz-answers.grade', $answer) }}"
                    class="mt-4 flex flex-wrap items-center justify-end gap-3">
                    @csrf
                    @method('PATCH')

                    <div class="flex items-center gap-1.5">
                        <label class="text-xs text-secondary">Marks</label>
                        <input type="number" name="points_awarded" min="0" max="{{ $answer->question->points }}" placeholder="0"
                            class="w-16 rounded-lg border-app-border text-center text-sm shadow-sm focus:border-primary focus:ring-primary" required>
                        <span class="text-xs text-secondary">/ {{ $answer->question->points }} pts</span>
                    </div>

                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-xs font-medium text-white hover:bg-primary-dark">
                        <x-icon name="check" class="w-3.5 h-3.5" />
                        Save Grade
                    </button>
                </form>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                Nothing pending review.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $pendingAnswers->links() }}
    </div>
</x-layout>
