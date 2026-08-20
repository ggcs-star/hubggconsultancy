@php
    $isEdit = (bool) $document;
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.documents.update', $document) : route('admin.documents.store') }}" class="p-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Document' : 'Add Document' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <div class="mt-6 space-y-5">
        <div>
            <label class="form-label">Title</label>
            <input type="text" name="title" value="{{ old('title', $isEdit ? $document->title : '') }}" required placeholder="e.g. GG Prime Presentation" class="form-input">
        </div>

        <div>
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-input" placeholder="Shown on the document card">{{ old('description', $isEdit ? $document->description : '') }}</textarea>
        </div>

        <div>
            <label class="form-label">Link</label>
            <input type="text" name="url" value="{{ old('url', $isEdit ? $document->url : '') }}" required placeholder="https://docs.google.com/..." class="form-input">
            <x-input-error :messages="$errors->get('url')" class="mt-1" />
            <p class="mt-1 text-xs text-slate-400">Google Docs/Slides/Sheets links, or any PDF/website link — opens in a new tab.</p>
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Save Changes' : 'Add Document' }}</button>
    </div>
</form>
