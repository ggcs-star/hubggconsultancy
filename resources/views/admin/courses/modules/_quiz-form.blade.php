@php
    $isEdit = (bool) $quiz;
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.course-module-quizzes.update', $quiz) : route('admin.course-module-quizzes.store', $module) }}" class="p-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-secondary-dark">{{ $isEdit ? 'Edit Module Quiz' : 'Add Module Quiz' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
            <x-icon name="x" class="w-5 h-5" />
        </button>
    </div>

    <div class="mt-6 space-y-5">
        <div>
            <x-input-label value="Title *" class="text-xs uppercase tracking-wide" />
            <x-text-input name="title" class="mt-1.5" :value="$isEdit ? $quiz->title : 'Quiz'" required autofocus />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div>
            <x-input-label value="Insert after *" class="text-xs uppercase tracking-wide" />
            <select name="after_course_lesson_id" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                <option value="" @selected($isEdit && is_null($quiz->after_course_lesson_id))>— At the start of this module —</option>
                @foreach ($module->lessons as $lesson)
                    <option value="{{ $lesson->id }}" @selected($isEdit && $quiz->after_course_lesson_id === $lesson->id)>{{ $lesson->title }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('after_course_lesson_id')" class="mt-2" />
        </div>

        <label class="flex items-center gap-2 text-sm text-secondary-dark">
            <input type="checkbox" name="is_required" value="1" @checked($isEdit ? $quiz->is_required : true) class="rounded border-app-border text-primary focus:ring-primary">
            Required to advance — learners must answer this quiz before "Next" continues to the following lesson
        </label>
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
        <x-primary-button>{{ $isEdit ? 'Save' : 'Add Quiz' }}</x-primary-button>
    </div>
</form>
