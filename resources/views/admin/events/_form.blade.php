@php
    $isEdit = (bool) $event;
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.events.update', $event) : route('admin.events.store') }}" class="flex max-h-[85vh] flex-col">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
        <h2 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Event' : 'Add Event' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <div class="space-y-5 overflow-y-auto px-6 py-6">
        <div>
            <label class="form-label">Title</label>
            <input type="text" name="title" value="{{ old('title', $isEdit ? $event->title : '') }}" required placeholder="e.g. GG Prime Business Opportunity" class="form-input">
        </div>

        <div>
            <label class="form-label">Subtitle</label>
            <input type="text" name="subtitle" value="{{ old('subtitle', $isEdit ? $event->subtitle : '') }}" placeholder="e.g. The Pitch – English, Live Training" class="form-input">
            <p class="mt-1 text-xs text-slate-400">Shown under the title on the event card.</p>
        </div>

        <div>
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-input" placeholder="Optional details shown to registrants">{{ old('description', $isEdit ? $event->description : '') }}</textarea>
        </div>

        <div>
            <label class="form-label">Location</label>
            <input type="text" name="location" value="{{ old('location', $isEdit ? $event->location : '') }}" placeholder="e.g. Zoom, Google Meet link, or a physical address" class="form-input">
        </div>

        @php
            $startsAtValue = old('starts_at', $isEdit ? $event->starts_at->format('Y-m-d\TH:i') : '');
            $endsAtValue = old('ends_at', $isEdit && $event->ends_at ? $event->ends_at->format('Y-m-d\TH:i') : '');
        @endphp

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2" x-data="{ startsAt: @js($startsAtValue) }">
            <div>
                <label class="form-label">Starts At</label>
                <input type="datetime-local" name="starts_at" x-model="startsAt" required class="form-input">
                <x-input-error :messages="$errors->get('starts_at')" class="mt-1" />
            </div>

            <div>
                <label class="form-label">Ends At</label>
                <input type="datetime-local" name="ends_at" value="{{ $endsAtValue }}" :min="startsAt" class="form-input">
                <x-input-error :messages="$errors->get('ends_at')" class="mt-1" />
                <p class="mt-1 text-xs text-slate-400">Must be on or after the start time.</p>
            </div>
        </div>

        <div>
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $isEdit ? $event->sort_order : '') }}" min="0" placeholder="0" class="form-input w-32">
            <p class="mt-1 text-xs text-slate-400">Lower numbers show first when dates tie. Leave blank to add it at the end.</p>
        </div>
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Save Changes' : 'Add Event' }}</button>
    </div>
</form>
