@php
    $isEdit = (bool) $announcement;
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.announcements.update', $announcement) : route('admin.announcements.store') }}" class="flex max-h-[85vh] flex-col">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
        <h2 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Announcement' : 'Post Announcement' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <div class="space-y-5 overflow-y-auto px-6 py-6">
        <div>
            <label class="form-label">Title</label>
            <input type="text" name="title" value="{{ old('title', $isEdit ? $announcement->title : '') }}" required placeholder="e.g. 100% First-Year Incentive now live!" class="form-input">
        </div>

        <div>
            <label class="form-label">Details <span class="font-normal text-slate-400">(optional)</span></label>
            <textarea name="body" rows="3" class="form-input" placeholder="Optional extra detail">{{ old('body', $isEdit ? $announcement->body : '') }}</textarea>
        </div>

        <div>
            <label class="form-label">Publish Date</label>
            <input type="date" name="published_at" value="{{ old('published_at', $isEdit ? $announcement->published_at->format('Y-m-d') : now()->format('Y-m-d')) }}" required class="form-input">
        </div>
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Save Changes' : 'Post Announcement' }}</button>
    </div>
</form>
