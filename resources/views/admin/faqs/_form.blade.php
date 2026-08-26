@php
    $isEdit = (bool) $faq;
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}" class="flex max-h-[85vh] flex-col">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
        <h2 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit FAQ' : 'Add FAQ' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <div class="space-y-5 overflow-y-auto px-6 py-6">
        <div>
            <label class="form-label">Section <span class="font-normal text-slate-400">(optional)</span></label>
            <div class="flex gap-2">
                <select name="faq_section_id" class="faq-section-select form-input flex-1">
                    <option value="">No section</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}" @selected(old('faq_section_id', $isEdit ? $faq->faq_section_id : '') == $section->id)>{{ $section->name }}</option>
                    @endforeach
                </select>
                <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'quick-add-faq-section')" title="Add Section" class="shrink-0 rounded-xl border border-slate-200 px-3 text-slate-500 transition hover:border-brand-300 hover:text-brand-700">
                    <x-icon name="plus" class="h-4 w-4" />
                </button>
            </div>
        </div>

        <div>
            <label class="form-label">Question</label>
            <input type="text" name="question" value="{{ old('question', $isEdit ? $faq->question : '') }}" required placeholder="e.g. How do I reset my password?" class="form-input">
        </div>

        <div>
            <label class="form-label">Answer</label>
            <textarea name="answer" rows="5" required class="form-input" placeholder="Write the answer shown to salespeople">{{ old('answer', $isEdit ? $faq->answer : '') }}</textarea>
        </div>

        <div>
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $isEdit ? $faq->sort_order : '') }}" min="0" placeholder="0" class="form-input w-32">
            <p class="mt-1 text-xs text-slate-400">Lower numbers show first. Leave blank to add it at the end.</p>
        </div>
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Save Changes' : 'Add FAQ' }}</button>
    </div>
</form>
