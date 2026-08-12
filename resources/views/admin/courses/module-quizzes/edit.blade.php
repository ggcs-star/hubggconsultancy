@php
    $questionTypeLabels = ['radio' => 'Single choice', 'checkbox' => 'Multiple choice', 'text' => 'Text answer'];
@endphp

<x-layout title="Edit Quiz">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.courses.index') }}" class="hover:text-primary">Courses</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <a href="{{ route('admin.courses.show', ['course' => $module->course, 'tab' => 'modules']) }}" class="hover:text-primary">{{ $module->course->title }}</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $quiz->title }}</span>
    </nav>

    <h1 class="mt-3 text-xl font-semibold text-secondary-dark">{{ $quiz->title }}</h1>
    <p class="text-sm text-secondary">Module quiz &middot; {{ $module->title }}</p>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-app-border bg-white p-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">Quiz Settings</h2>
                <form method="POST" action="{{ route('admin.course-module-quizzes.update', $quiz) }}" class="mt-4 space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="title" value="Title *" class="uppercase text-xs tracking-wide" />
                        <x-text-input id="title" name="title" class="mt-1.5" :value="old('title', $quiz->title)" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="after_course_lesson_id" value="Insert after *" class="uppercase text-xs tracking-wide" />
                        <select id="after_course_lesson_id" name="after_course_lesson_id"
                            class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                            <option value="" @selected(is_null(old('after_course_lesson_id', $quiz->after_course_lesson_id)))>— At the start of this module —</option>
                            @foreach ($module->lessons as $lesson)
                                <option value="{{ $lesson->id }}" @selected(old('after_course_lesson_id', $quiz->after_course_lesson_id) == $lesson->id)>{{ $lesson->title }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('after_course_lesson_id')" class="mt-2" />
                    </div>

                    <label class="flex items-center gap-2 text-sm text-secondary-dark">
                        <input type="checkbox" name="is_required" value="1" @checked(old('is_required', $quiz->is_required)) class="rounded border-app-border text-primary focus:ring-primary">
                        Required to advance — learners must answer this quiz before "Next" continues to the following lesson
                    </label>

                    <div class="flex justify-end">
                        <x-primary-button>Save Quiz</x-primary-button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">Questions</h2>
                    <p class="mt-1 text-xs text-secondary">Shown together — learners answer all of them, then submit at once.</p>
                </div>
                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'add-question')"
                    class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-primary/30 bg-primary-light px-3 py-2 text-xs font-medium text-primary hover:border-primary/60 hover:bg-primary/20">
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                    Add Question
                </button>
            </div>

            <div class="mt-4 rounded-xl border border-app-border bg-white">
                <div class="divide-y divide-app-border">
                    @forelse ($quiz->questions as $question)
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
                            @include('admin.courses.lessons._question-form', ['checkpoint' => $quiz, 'question' => $question])
                        </x-modal>
                    @empty
                        <p class="px-4 py-6 text-center text-xs text-secondary">No questions yet — click "Add Question" to create the first one.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <x-modal name="add-question" maxWidth="lg">
        @include('admin.courses.lessons._question-form', ['checkpoint' => $quiz, 'question' => null])
    </x-modal>
</x-layout>
