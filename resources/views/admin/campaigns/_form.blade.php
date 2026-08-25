@php
    $isEdit = (bool) $campaign;
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.campaigns.update', $campaign) : route('admin.campaigns.store') }}" class="flex max-h-[85vh] flex-col">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
        <h2 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Campaign' : 'Add Campaign' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <div class="space-y-5 overflow-y-auto px-6 py-6">
        <div>
            <label class="form-label">Campaign Name</label>
            <input type="text" name="name" value="{{ old('name', $isEdit ? $campaign->name : '') }}" required placeholder="e.g. GG Prime August Campaign" class="form-input">
        </div>

        <div>
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-input" placeholder="Optional notes about this campaign">{{ old('description', $isEdit ? $campaign->description : '') }}</textarea>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label class="form-label">Starts On</label>
                <input type="date" name="starts_at" value="{{ old('starts_at', $isEdit && $campaign->starts_at ? $campaign->starts_at->format('Y-m-d') : '') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Ends On</label>
                <input type="date" name="ends_at" value="{{ old('ends_at', $isEdit && $campaign->ends_at ? $campaign->ends_at->format('Y-m-d') : '') }}" class="form-input">
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Save Changes' : 'Add Campaign' }}</button>
    </div>
</form>
