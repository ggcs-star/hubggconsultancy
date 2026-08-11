@php
    $question = $question ?? null;
    $isEdit = (bool) $question;
    $initialOptions = $isEdit && $question->type !== 'text'
        ? $question->options->map(fn ($option) => ['text' => $option->option_text, 'correct' => $option->is_correct])->values()
        : collect([['text' => '', 'correct' => false], ['text' => '', 'correct' => false]]);
@endphp

<form method="POST"
    action="{{ $isEdit ? route('admin.course-quiz-questions.update', $question) : route('admin.course-quiz-questions.store', $checkpoint) }}"
    x-data="{
        type: '{{ $isEdit ? $question->type : 'radio' }}',
        options: {{ \Illuminate\Support\Js::from($initialOptions) }},
        addOption() { this.options.push({ text: '', correct: false }); },
        removeOption(index) { this.options.splice(index, 1); },
        selectSingle(index) { this.options.forEach((o, i) => o.correct = i === index); },
    }"
    class="p-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-secondary-dark">{{ $isEdit ? 'Edit Question' : 'Add Question' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
            <x-icon name="x" class="w-5 h-5" />
        </button>
    </div>

    <div class="mt-6 space-y-5">
        <div>
            <x-input-label value="Question Type *" class="text-xs uppercase tracking-wide" />
            <select name="type" x-model="type" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                <option value="radio">Single choice (radio)</option>
                <option value="checkbox">Multiple choice (checkbox)</option>
                <option value="text">Text answer (manually graded)</option>
            </select>
        </div>

        <div>
            <x-input-label value="Question Text *" class="text-xs uppercase tracking-wide" />
            <textarea name="question_text" rows="2" required
                class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ $isEdit ? $question->question_text : old('question_text') }}</textarea>
            <x-input-error :messages="$errors->get('question_text')" class="mt-2" />
        </div>

        <div class="w-32">
            <x-input-label value="Points *" class="text-xs uppercase tracking-wide" />
            <input type="number" name="points" min="1" max="1000" value="{{ old('points', $isEdit ? $question->points : 1) }}" required
                class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
            <x-input-error :messages="$errors->get('points')" class="mt-2" />
        </div>

        <div x-show="type !== 'text'" x-cloak>
            <div class="flex items-center justify-between">
                <x-input-label value="Options — mark the correct answer(s) *" class="text-xs uppercase tracking-wide" />
                <button type="button" x-on:click="addOption()" class="text-xs font-medium text-primary hover:underline">+ Add option</button>
            </div>

            <div class="mt-2 space-y-2">
                <template x-for="(option, index) in options" :key="index">
                    <div class="flex items-center gap-2">
                        <template x-if="type === 'radio'">
                            <input type="radio" name="correct[]" :value="index" :checked="option.correct" @change="selectSingle(index)"
                                class="border-app-border text-primary focus:ring-primary">
                        </template>
                        <template x-if="type === 'checkbox'">
                            <input type="checkbox" name="correct[]" :value="index" x-model="option.correct"
                                class="rounded border-app-border text-primary focus:ring-primary" :true-value="true" :false-value="false">
                        </template>
                        <input type="text" x-model="option.text" :name="'options[' + index + ']'" placeholder="Option text"
                            class="flex-1 rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                        <button type="button" x-on:click="removeOption(index)" x-show="options.length > 2" class="text-secondary hover:text-danger">
                            <x-icon name="x" class="w-4 h-4" />
                        </button>
                    </div>
                </template>
            </div>
            <x-input-error :messages="$errors->get('options')" class="mt-2" />
            <x-input-error :messages="$errors->get('correct')" class="mt-2" />
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
        <x-primary-button>{{ $isEdit ? 'Save' : 'Add Question' }}</x-primary-button>
    </div>
</form>
