@php
    $question = $question ?? null;
    $isEdit = (bool) $question;
    $initialOptions = $isEdit && $question->type !== 'text'
        ? $question->options->map(fn ($option) => ['text' => $option->option_text, 'correct' => $option->is_correct])->values()
        : collect([['text' => '', 'correct' => false], ['text' => '', 'correct' => false]]);
@endphp

<form method="POST"
    action="{{ $isEdit ? route('admin.resource-quiz-questions.update', $question) : route('admin.resource-quiz-questions.store', $checkpoint) }}"
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
        <h2 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Question' : 'Add Question' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <div class="mt-6 space-y-5">
        <div>
            <label class="form-label">Question Type</label>
            <select name="type" x-model="type" class="form-input">
                <option value="radio">Single choice (radio)</option>
                <option value="checkbox">Multiple choice (checkbox)</option>
                <option value="text">Text answer (manually graded)</option>
            </select>
        </div>

        <div>
            <label class="form-label">Question Text</label>
            <textarea name="question_text" rows="2" required class="form-input">{{ $isEdit ? $question->question_text : old('question_text') }}</textarea>
            <x-input-error :messages="$errors->get('question_text')" class="mt-1" />
        </div>

        <div class="w-32">
            <label class="form-label">Points</label>
            <input type="number" name="points" min="1" max="1000" value="{{ old('points', $isEdit ? $question->points : 1) }}" required class="form-input">
            <x-input-error :messages="$errors->get('points')" class="mt-1" />
        </div>

        <div x-show="type !== 'text'" x-cloak>
            <div class="flex items-center justify-between">
                <label class="form-label mb-0">Options — mark the correct answer(s)</label>
                <button type="button" x-on:click="addOption()" class="text-xs font-semibold text-brand-700 hover:text-brand-800">+ Add option</button>
            </div>

            <div class="mt-2 space-y-2">
                <template x-for="(option, index) in options" :key="index">
                    <div class="flex items-center gap-2">
                        <template x-if="type === 'radio'">
                            <input type="radio" name="correct[]" :value="index" :checked="option.correct" @change="selectSingle(index)" class="border-slate-300 text-brand-600 focus:ring-brand-500">
                        </template>
                        <template x-if="type === 'checkbox'">
                            <input type="checkbox" name="correct[]" :value="index" x-model="option.correct" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500" :true-value="true" :false-value="false">
                        </template>
                        <input type="text" x-model="option.text" :name="'options[' + index + ']'" placeholder="Option text" class="form-input flex-1">
                        <button type="button" x-on:click="removeOption(index)" x-show="options.length > 2" class="text-slate-400 hover:text-red-600">
                            <x-icon name="x" class="h-4 w-4" />
                        </button>
                    </div>
                </template>
            </div>
            <x-input-error :messages="$errors->get('options')" class="mt-1" />
            <x-input-error :messages="$errors->get('correct')" class="mt-1" />
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Save Changes' : 'Add Question' }}</button>
    </div>
</form>
