@php
    $isEdit = (bool) $item;
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.onboarding-checklist.update', $item) : route('admin.onboarding-checklist.store') }}" class="flex max-h-[85vh] flex-col">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
        <h2 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Checklist Item' : 'Add Checklist Item' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <div class="space-y-5 overflow-y-auto px-6 py-6">
        <div>
            <label class="form-label">Title</label>
            <input type="text" name="title" value="{{ old('title', $isEdit ? $item->title : '') }}" required placeholder="e.g. Watch the welcome video" class="form-input">
        </div>

        <div>
            <label class="form-label">Description <span class="font-normal text-slate-400">(optional)</span></label>
            <textarea name="description" rows="3" class="form-input" placeholder="What should the salesperson do for this step?">{{ old('description', $isEdit ? $item->description : '') }}</textarea>
        </div>

        <div>
            <label class="form-label">Link <span class="font-normal text-slate-400">(optional)</span></label>
            <input type="text" name="link" value="{{ old('link', $isEdit ? $item->link : '') }}" placeholder="https://... (video, document, etc.)" class="form-input">
            <x-input-error :messages="$errors->get('link')" class="mt-1" />
            <p class="mt-1 text-xs text-slate-400">If set, a button lets the salesperson open the video/document directly from the checklist.</p>
        </div>

        <div>
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $isEdit ? $item->sort_order : '') }}" min="0" placeholder="0" class="form-input w-32">
            <p class="mt-1 text-xs text-slate-400">Lower numbers show first. Leave blank to add it at the end.</p>
        </div>
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Save Changes' : 'Add Checklist Item' }}</button>
    </div>
</form>
