@php
    $isEdit = (bool) $checkpoint;
    $minutes = $isEdit ? intdiv($checkpoint->timestamp_seconds, 60) : 0;
    $seconds = $isEdit ? $checkpoint->timestamp_seconds % 60 : 0;
    $formLanguage = $isEdit ? $checkpoint->language : $language;
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.resource-checkpoints.update', $checkpoint) : route('admin.resource-checkpoints.store', $resource) }}" class="p-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif
    <input type="hidden" name="language" value="{{ $formLanguage }}">

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Checkpoint' : 'Add Checkpoint' }} — {{ ucfirst($formLanguage) }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <div class="mt-6 space-y-5">
        <div>
            <label class="form-label">Pause video at</label>
            <div class="flex items-center gap-2">
                <input type="number" name="minutes" min="0" value="{{ $minutes }}" placeholder="min" required class="form-input w-24">
                <span class="text-slate-400">:</span>
                <input type="number" name="seconds" min="0" max="59" value="{{ $seconds }}" placeholder="sec" required class="form-input w-24">
                <span class="text-xs text-slate-400">minutes : seconds</span>
            </div>
            <x-input-error :messages="$errors->get('minutes')" class="mt-1" />
        </div>

        <div>
            <label class="form-label">Label (optional)</label>
            <input type="text" name="title" value="{{ $isEdit ? $checkpoint->title : '' }}" placeholder="e.g. Quick Check 1" class="form-input">
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Save Changes' : 'Add Checkpoint' }}</button>
    </div>
</form>
