@php
    $isEdit = (bool) $item;
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.sales-toolkit.update', $item) : route('admin.sales-toolkit.store') }}" enctype="multipart/form-data" class="flex max-h-[85vh] flex-col">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
        <h2 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Toolkit Item' : 'Add Toolkit Item' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <div class="space-y-5 overflow-y-auto px-6 py-6">
        <div>
            <label class="form-label">Title</label>
            <input type="text" name="title" value="{{ old('title', $isEdit ? $item->title : '') }}" required placeholder="e.g. Cold Call Script" class="form-input">
        </div>

        <div>
            <label class="form-label">Category</label>
            <input type="text" name="category" value="{{ old('category', $isEdit ? $item->category : '') }}" list="sales-toolkit-categories" placeholder="e.g. Scripts, Pitch Decks, Email Templates" class="form-input">
            <datalist id="sales-toolkit-categories">
                @foreach ($categories as $category)
                    <option value="{{ $category }}"></option>
                @endforeach
            </datalist>
            <p class="mt-1 text-xs text-slate-400">Groups items on the salesperson's toolkit page. Leave blank for "General".</p>
        </div>

        <div>
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-input" placeholder="Shown on the item card">{{ old('description', $isEdit ? $item->description : '') }}</textarea>
        </div>

        <div x-data="{ fileName: null }">
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
                    <span class="block text-xs text-slate-400">PDF, Word, PowerPoint, image — up to 20MB</span>
                </span>
                <input type="file" name="file" {{ $isEdit ? '' : 'required' }} class="hidden" x-on:change="fileName = $event.target.files[0]?.name ?? null">
            </label>
            <x-input-error :messages="$errors->get('file')" class="mt-1" />
        </div>

        <div>
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
            <p class="mt-1 text-xs text-slate-400">Lower numbers show first. Leave blank to add it at the end.</p>
        </div>
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Save Changes' : 'Add Item' }}</button>
    </div>
</form>
