@php
    $isEdit = (bool) $checkpoint;
    $minutes = $isEdit ? intdiv($checkpoint->timestamp_seconds, 60) : 0;
    $seconds = $isEdit ? $checkpoint->timestamp_seconds % 60 : 0;
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.course-quiz-checkpoints.update', $checkpoint) : route('admin.course-quiz-checkpoints.store', $lesson) }}" class="p-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-secondary-dark">{{ $isEdit ? 'Edit Checkpoint' : 'Add Checkpoint' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
            <x-icon name="x" class="w-5 h-5" />
        </button>
    </div>

    <div class="mt-6 space-y-5">
        <div>
            <x-input-label value="Pause video at *" class="text-xs uppercase tracking-wide" />
            <div class="mt-1.5 flex items-center gap-2">
                <input type="number" name="minutes" min="0" value="{{ $minutes }}" placeholder="min"
                    class="w-20 rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary" required>
                <span class="text-secondary">:</span>
                <input type="number" name="seconds" min="0" max="59" value="{{ $seconds }}" placeholder="sec"
                    class="w-20 rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary" required>
                <span class="text-xs text-secondary">minutes : seconds</span>
            </div>
            <x-input-error :messages="$errors->get('minutes')" class="mt-2" />
        </div>
        <div>
            <x-input-label value="Label (optional)" class="text-xs uppercase tracking-wide" />
            <x-text-input name="title" class="mt-1.5" :value="$isEdit ? $checkpoint->title : ''" placeholder="e.g. Checkpoint 1" />
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
        <x-primary-button>{{ $isEdit ? 'Save' : 'Add Checkpoint' }}</x-primary-button>
    </div>
</form>
