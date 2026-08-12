@php
    $questionTypeLabels = ['radio' => 'Single choice', 'checkbox' => 'Multiple choice', 'text' => 'Text answer'];
@endphp

<x-layout title="Edit Lesson">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.courses.index') }}" class="hover:text-primary">Courses</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <a href="{{ route('admin.courses.show', ['course' => $courseModule->course, 'tab' => 'modules']) }}" class="hover:text-primary">{{ $courseModule->course->title }}</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $lesson->title }}</span>
    </nav>

    <h1 class="mt-3 text-xl font-semibold text-secondary-dark">{{ $lesson->title }}</h1>
    <p class="text-sm text-secondary">Module: {{ $courseModule->title }}</p>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-app-border bg-white p-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">Lesson Details</h2>
                <form method="POST" action="{{ route('admin.course-lessons.update', $lesson) }}" enctype="multipart/form-data"
                    x-data="{ source: '{{ old('video_source', $lesson->video_source) }}' }" class="mt-4 space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="title" value="Title *" class="uppercase text-xs tracking-wide" />
                        <x-text-input id="title" name="title" class="mt-1.5" :value="old('title', $lesson->title)" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Description" class="uppercase text-xs tracking-wide" />
                        <textarea id="description" name="description" rows="3"
                            class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('description', $lesson->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    @include('admin.courses.lessons._video-fields')

                    <div>
                        <x-input-label for="duration" value="Duration (display label, e.g. 2:30)" class="uppercase text-xs tracking-wide" />
                        <x-text-input id="duration" name="duration" class="mt-1.5" :value="old('duration', $lesson->duration)" placeholder="2:30" />
                        <x-input-error :messages="$errors->get('duration')" class="mt-2" />
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>Save Lesson</x-primary-button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">Quiz Checkpoints</h2>
                    <p class="mt-1 text-xs text-secondary">Playback pauses at each timestamp and shows its question(s).</p>
                </div>
                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'add-checkpoint')"
                    class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-primary/30 bg-primary-light px-3 py-2 text-xs font-medium text-primary hover:border-primary/60 hover:bg-primary/20">
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                    Add Checkpoint
                </button>
            </div>

            <div class="mt-4 space-y-4">
                @forelse ($lesson->checkpoints as $checkpoint)
                    <div class="rounded-xl border border-app-border bg-white">
                        <div class="flex items-center justify-between gap-3 px-4 py-3">
                            <div class="flex items-center gap-2">
                                <x-badge classes="bg-surface-alt text-secondary-dark">
                                    {{ sprintf('%d:%02d', intdiv($checkpoint->timestamp_seconds, 60), $checkpoint->timestamp_seconds % 60) }}
                                </x-badge>
                                <span class="text-sm font-medium text-secondary-dark">{{ $checkpoint->title ?: 'Checkpoint' }}</span>
                                <span class="text-xs text-secondary">{{ $checkpoint->questions->count() }} {{ Str::plural('question', $checkpoint->questions->count()) }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'edit-checkpoint-{{ $checkpoint->id }}')"
                                    class="rounded-md p-1.5 text-secondary hover:bg-surface-alt hover:text-secondary-dark" title="Edit Timestamp">
                                    <x-icon name="edit" class="w-3.5 h-3.5" />
                                </button>
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'add-question-{{ $checkpoint->id }}')"
                                    class="rounded-md p-1.5 text-primary hover:bg-primary-light" title="Add Question">
                                    <x-icon name="plus" class="w-3.5 h-3.5" />
                                </button>
                                <form method="POST" action="{{ route('admin.course-quiz-checkpoints.destroy', $checkpoint) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete this checkpoint and its questions?', target: $el })">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md p-1.5 text-secondary hover:bg-danger-light hover:text-danger" title="Delete checkpoint">
                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="divide-y divide-app-border border-t border-app-border">
                            @forelse ($checkpoint->questions as $question)
                                <div class="px-4 py-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <span class="text-xs font-medium uppercase tracking-wide text-secondary">{{ $questionTypeLabels[$question->type] }}</span>
                                            <span class="text-xs text-secondary">&middot; {{ $question->points }} {{ Str::plural('pt', $question->points) }}</span>
                                            <p class="text-sm text-secondary-dark">{{ $question->question_text }}</p>
                                            @if ($question->type !== 'text')
                                                <ul class="mt-1.5 space-y-0.5">
                                                    @foreach ($question->options as $option)
                                                        <li class="flex items-center gap-1.5 text-xs {{ $option->is_correct ? 'text-success font-medium' : 'text-secondary' }}">
                                                            <x-icon name="{{ $option->is_correct ? 'check' : 'x' }}" class="w-3 h-3" />
                                                            {{ $option->option_text }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="mt-1 text-xs italic text-secondary">Graded manually by an admin from the Pending Reviews queue.</p>
                                            @endif
                                        </div>
                                        <div class="flex shrink-0 items-center gap-1">
                                            <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'edit-question-{{ $question->id }}')"
                                                class="rounded-md p-1.5 text-secondary hover:bg-surface-alt hover:text-secondary-dark" title="Edit question">
                                                <x-icon name="edit" class="w-3.5 h-3.5" />
                                            </button>
                                            <form method="POST" action="{{ route('admin.course-quiz-questions.destroy', $question) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete this question?', target: $el })">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-md p-1.5 text-secondary hover:bg-danger-light hover:text-danger" title="Delete question">
                                                    <x-icon name="trash" class="w-3.5 h-3.5" />
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <x-modal name="edit-question-{{ $question->id }}" maxWidth="lg">
                                    @include('admin.courses.lessons._question-form', ['checkpoint' => $checkpoint, 'question' => $question])
                                </x-modal>
                            @empty
                                <p class="px-4 py-3 text-xs text-secondary">No questions yet — click "+" to add one.</p>
                            @endforelse
                        </div>
                    </div>

                    <x-modal name="edit-checkpoint-{{ $checkpoint->id }}" maxWidth="sm">
                        @include('admin.courses.lessons._checkpoint-form', ['checkpoint' => $checkpoint])
                    </x-modal>

                    <x-modal name="add-question-{{ $checkpoint->id }}" maxWidth="lg">
                        @include('admin.courses.lessons._question-form', ['checkpoint' => $checkpoint, 'question' => null])
                    </x-modal>
                @empty
                    <div class="rounded-xl border border-dashed border-app-border bg-white p-8 text-center text-sm text-secondary">
                        No checkpoints yet. Add one to pause the video with a quiz at a specific time.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <x-modal name="add-checkpoint" maxWidth="sm">
        @include('admin.courses.lessons._checkpoint-form', ['checkpoint' => null])
    </x-modal>
</x-layout>
