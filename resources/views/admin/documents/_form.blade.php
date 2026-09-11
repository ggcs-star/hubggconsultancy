@php
    $isEdit = (bool) $document;
    $initialSource = old('source', $isEdit && ! $document->is_external ? 'upload' : 'link');
@endphp

<form
    method="POST"
    action="{{ $isEdit ? route('admin.documents.update', $document) : route('admin.documents.store') }}"
    enctype="multipart/form-data"
    class="p-6"
    x-data="{ source: '{{ $initialSource }}', fileName: null }"
>
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
            <label class="form-label">Language</label>
            <x-language-select :selected="old('language', $isEdit ? $document->language : 'english')" />
            <x-input-error :messages="$errors->get('language')" class="mt-1" />
        </div>

        <div>
            <label class="form-label">Content Source</label>
            <div class="flex gap-3">
                <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                    <input type="radio" name="source" value="link" x-model="source" class="text-brand-600">
                    Provide Link
                </label>
                <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                    <input type="radio" name="source" value="upload" x-model="source" class="text-brand-600">
                    Upload File
                </label>
            </div>
        </div>

        <div x-show="source === 'link'" x-cloak>
            <label class="form-label">Link</label>
            <input type="text" name="url" value="{{ old('url', $isEdit && $document->is_external ? $document->url : '') }}" placeholder="https://docs.google.com/..." class="form-input">
            <x-input-error :messages="$errors->get('url')" class="mt-1" />
            <p class="mt-1 text-xs text-slate-400">Google Docs/Slides/Sheets links, or any PDF/website link.</p>
        </div>

        <div x-show="source === 'upload'" x-cloak>
            <label class="form-label">File</label>

            @if ($isEdit && $document->original_filename)
                <div class="mb-2 flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-600">
                    <x-icon name="document" class="h-4 w-4 shrink-0 text-slate-400" />
                    <a href="{{ $document->fileUrl() }}" target="_blank" rel="noopener" class="truncate text-brand-700 hover:underline">{{ $document->original_filename }}</a>
                </div>
            @endif

            <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 border-dashed border-slate-200 px-4 py-3.5 transition hover:border-brand-300 hover:bg-brand-50/40">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                    <x-icon name="download" class="h-5 w-5 rotate-180" />
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-sm font-medium text-slate-700" x-text="fileName || '{{ $isEdit ? 'Choose a new file…' : 'Choose a file…' }}'"></span>
                    <span class="block text-xs text-slate-400">PDF, Word, PowerPoint, image — up to 50MB</span>
                </span>
                <input type="file" name="file" class="hidden" x-on:change="fileName = $event.target.files[0]?.name ?? null">
            </label>
            <x-input-error :messages="$errors->get('file')" class="mt-1" />
        </div>

        <div>
            <label class="form-label">Thumbnail <span class="font-normal text-slate-400">(optional)</span></label>
            @if ($isEdit && $document->thumbnailUrl())
                <img src="{{ $document->thumbnailUrl() }}" alt="" class="mb-2 h-20 w-full rounded-lg object-cover">
            @endif
            <input type="file" name="thumbnail" accept="image/*" class="form-input">
            <x-input-error :messages="$errors->get('thumbnail')" class="mt-1" />
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Save Changes' : 'Add Document' }}</button>
    </div>
</form>
