@php
    $question = $question ?? null;
    $isEdit = (bool) $question;
    $initialOptions = $isEdit && $question->type !== 'text'
        ? $question->options->map(fn ($option) => ['text' => $option->option_text, 'correct' => $option->is_correct])->values()
        : collect([['text' => '', 'correct' => false], ['text' => '', 'correct' => false]]);
@endphp

<form method="POST"
    action="{{ $isEdit ? route('admin.onboarding-assessment.questions.update', $question) : route('admin.onboarding-assessment.questions.store', $quiz) }}"
    x-data="{
        type: '{{ $isEdit ? $question->type : 'radio' }}',
        options: {{ \Illuminate\Support\Js::from($initialOptions) }},
        submitting: false,
        addOption() { this.options.push({ text: '', correct: false }); },
        removeOption(index) { this.options.splice(index, 1); },
        selectSingle(index) { this.options.forEach((o, i) => o.correct = i === index); },
    }"
    x-on:submit="submitting = true"
    class="p-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif
    <input type="hidden" name="_form" value="question">
    <input type="hidden" name="_quiz_id" value="{{ $quiz->id }}">

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
        </div>

        <div class="w-32">
            <label class="form-label">Points</label>
            <input type="number" name="points" min="1" max="1000" value="{{ old('points', $isEdit ? $question->points : 1) }}" required class="form-input">
        </div>

        <div x-show="type !== 'text'" x-cloak>
            <div class="flex items-center justify-between">
                <label class="form-label mb-0">Options — mark the correct answer(s)</label>
                <button type="button" x-on:click="addOption()" class="text-xs font-semibold text-brand-700 hover:underline">+ Add option</button>
            </div>

            <div class="mt-2 space-y-2">
                <template x-for="(option, index) in options" :key="index">
                    <div class="flex items-center gap-2">
                        <template x-if="type === 'radio'">
                            <input type="radio" name="correct[]" :value="index" :checked="option.correct" @change="selectSingle(index)"
                                class="border-slate-300 text-brand-600 focus:ring-brand-500">
                        </template>
                        <template x-if="type === 'checkbox'">
                            <input type="checkbox" name="correct[]" :value="index" x-model="option.correct"
                                class="rounded border-slate-300 text-brand-600 focus:ring-brand-500" :true-value="true" :false-value="false">
                        </template>
                        <input type="text" x-model="option.text" :name="'options[' + index + ']'" placeholder="Option text" class="form-input flex-1">
                        <button type="button" x-on:click="removeOption(index)" x-show="options.length > 2" class="text-slate-400 hover:text-red-600">
                            <x-icon name="x" class="h-4 w-4" />
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" :disabled="submitting" class="btn-primary disabled:cursor-not-allowed disabled:opacity-50">
            <span x-text="submitting ? 'Saving…' : '{{ $isEdit ? 'Save Changes' : 'Add Question' }}'"></span>
        </button>
    </div>
</form>
