@php
    $isEdit = (bool) $topic;
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.scripts.update', $topic) : route('admin.scripts.store') }}" class="flex max-h-[85vh] flex-col">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
        <h2 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Topic' : 'Add Topic' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <div class="space-y-5 overflow-y-auto px-6 py-6">
        <div>
            <label class="form-label">Topic Title</label>
            <input type="text" name="title" value="{{ old('title', $isEdit ? $topic->title : '') }}" required placeholder="e.g. Price Objection" class="form-input">
        </div>

        <div>
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $isEdit ? $topic->sort_order : '') }}" min="0" placeholder="0" class="form-input w-32">
            <p class="mt-1 text-xs text-slate-400">Lower numbers show first. Leave blank to add it at the end.</p>
        </div>
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Save Changes' : 'Add Topic' }}</button>
    </div>
</form>
