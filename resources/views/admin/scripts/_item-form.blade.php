@php
    $isEdit = (bool) $item;
    $initialType = old('type', $isEdit ? $item->type : 'video');
    $initialSource = old('source', $isEdit && $item->is_external ? 'link' : 'upload');
@endphp

<form
    method="POST"
    action="{{ $isEdit ? route('admin.script-items.update', $item) : route('admin.script-items.store', $topic) }}"
    enctype="multipart/form-data"
    class="flex max-h-[85vh] flex-col"
    x-data="{ type: '{{ $initialType }}', source: '{{ $initialSource }}', fileName: null }"
>
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
        <h2 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Item' : 'Add Item' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <div class="space-y-5 overflow-y-auto px-6 py-6">
        <div>
            <label class="form-label">Type</label>
            <div class="flex gap-3">
                <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                    <input type="radio" name="type" value="video" x-model="type" class="text-brand-600">
                    <x-icon name="video" class="h-4 w-4" />
                    Video
                </label>
                <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                    <input type="radio" name="type" value="document" x-model="type" class="text-brand-600">
                    <x-icon name="document" class="h-4 w-4" />
                    Document
                </label>
            </div>
        </div>

        <div>
            <label class="form-label">Title</label>
            <input type="text" name="title" value="{{ old('title', $isEdit ? $item->title : '') }}" required placeholder="e.g. How to Handle &quot;Your Price Is Too High&quot;" class="form-input">
        </div>

        <div>
            <label class="form-label">Language</label>
            <select name="language" class="form-input">
                @php $selectedLanguage = old('language', $isEdit ? $item->language : 'english'); @endphp
                <option value="english" @selected($selectedLanguage === 'english')>English</option>
                <option value="hindi" @selected($selectedLanguage === 'hindi')>Hindi</option>
                <option value="gujarati" @selected($selectedLanguage === 'gujarati')>Gujarati</option>
            </select>
            <x-input-error :messages="$errors->get('language')" class="mt-1" />
        </div>

        <div x-show="type === 'video'" x-cloak>
            <label class="form-label">Video Source</label>
            <div class="flex gap-3">
                <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                    <input type="radio" name="source" value="upload" x-model="source" class="text-brand-600">
                    Upload File
                </label>
                <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                    <input type="radio" name="source" value="link" x-model="source" class="text-brand-600">
                    Provide Video URL
                </label>
            </div>
        </div>

        <div x-show="type === 'video' && source === 'link'" x-cloak>
            <label class="form-label">Video URL</label>
            <input type="url" name="video_url" value="{{ old('video_url', $isEdit && $item->is_external ? $item->url : '') }}" placeholder="https://youtube.com/watch?v=..." class="form-input">
            <p class="mt-1 text-xs text-slate-400">YouTube, Vimeo, Google Drive or any direct video link.</p>
            <x-input-error :messages="$errors->get('video_url')" class="mt-1" />
        </div>

        <div x-show="!(type === 'video' && source === 'link')" x-cloak>
            <label class="form-label">File</label>

            @if ($isEdit && $item->original_filename)
                <div class="mb-2 flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-600">
                    <x-icon name="document" class="h-4 w-4 shrink-0 text-slate-400" />
                    <a href="{{ $item->fileUrl() }}" target="_blank" rel="noopener" class="truncate text-brand-700 hover:underline">{{ $item->original_filename }}</a>
                </div>
            @endif

            <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 border-dashed border-slate-200 px-4 py-3.5 transition hover:border-brand-300 hover:bg-brand-50/40">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                    <x-icon name="download" class="h-5 w-5 rotate-180" />
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-sm font-medium text-slate-700" x-text="fileName || '{{ $isEdit ? 'Choose a new file…' : 'Choose a file…' }}'"></span>
                    <span class="block text-xs text-slate-400" x-text="type === 'video' ? 'Video file — up to 1GB' : 'Document file — up to 50MB'"></span>
                </span>
                <input type="file" name="file" class="hidden" x-on:change="fileName = $event.target.files[0]?.name ?? null">
            </label>
            <x-input-error :messages="$errors->get('file')" class="mt-1" />
        </div>

        <div x-show="type === 'document'" x-cloak>
            <label class="form-label">Thumbnail <span class="font-normal text-slate-400">(optional)</span></label>
            @if ($isEdit && $item->thumbnailUrl())
                <img src="{{ $item->thumbnailUrl() }}" alt="" class="mb-2 h-20 w-full rounded-lg object-cover">
            @endif
            <input type="file" name="thumbnail" accept="image/*" class="form-input">
            <p class="mt-1 text-xs text-slate-400">Shown instead of the generic file icon in the list.</p>
            <x-input-error :messages="$errors->get('thumbnail')" class="mt-1" />
        </div>

        <div>
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $isEdit ? $item->sort_order : '') }}" min="0" placeholder="0" class="form-input w-32">
        </div>
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Save Changes' : 'Add Item' }}</button>
    </div>
</form>
