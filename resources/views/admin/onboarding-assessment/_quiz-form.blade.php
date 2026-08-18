@php
    $quiz = $quiz ?? null;
    $isEdit = (bool) $quiz;
@endphp

<form method="POST"
    action="{{ $isEdit ? route('admin.onboarding-assessment.quizzes.update', $quiz) : route('admin.onboarding-assessment.quizzes.store') }}"
    x-data="{ submitting: false }"
    x-on:submit="submitting = true"
    class="p-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif
    <input type="hidden" name="_form" value="quiz">
    @if ($isEdit)
        <input type="hidden" name="_edit_id" value="{{ $quiz->id }}">
    @endif

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Quiz' : 'Add Quiz' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <div class="mt-6 space-y-5">
        <div>
            <label class="form-label">Title</label>
            <input type="text" name="title" value="{{ old('title', $isEdit ? $quiz->title : '') }}" required placeholder="e.g. Sales Quiz" class="form-input">
        </div>

        <div>
            <label class="form-label">Description</label>
            <textarea name="description" rows="2" class="form-input" placeholder="Optional — shown to salespeople before they start this quiz">{{ old('description', $isEdit ? $quiz->description : '') }}</textarea>
        </div>

        @if ($isEdit)
            <label class="flex items-center gap-2 text-sm font-medium text-slate-600">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $quiz->is_published)) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                Published — visible to salespeople
            </label>
            <p class="text-xs text-slate-400">The overall assessment must also be published (Settings tab) for this quiz to actually be reachable.</p>
        @else
            <p class="text-xs text-slate-400">New quizzes start as a draft — publish it afterward from its edit screen once its questions are ready.</p>
        @endif
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" :disabled="submitting" class="btn-primary disabled:cursor-not-allowed disabled:opacity-50">
            <span x-text="submitting ? 'Saving…' : '{{ $isEdit ? 'Save Changes' : 'Add Quiz' }}'"></span>
        </button>
    </div>
</form>
