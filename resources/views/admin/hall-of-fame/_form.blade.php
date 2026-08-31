@php
    $isEdit = (bool) ($entry ?? null);
@endphp

<form
    method="POST"
    action="{{ $isEdit ? route('admin.hall-of-fame.update', $entry) : route('admin.hall-of-fame.store') }}"
    class="flex max-h-[85vh] flex-col"
    enctype="multipart/form-data"
>
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
        <h2 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Hall of Fame Entry' : 'Add Hall of Fame Entry' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <div class="space-y-5 overflow-y-auto px-6 py-6">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label class="form-label">Rank <span class="text-red-500">*</span> <span class="font-normal text-slate-400">(tiebreaker)</span></label>
                <input type="number" name="rank" min="1" value="{{ old('rank', $isEdit ? $entry->rank : '') }}" required class="form-input" placeholder="e.g. 1">
                <x-input-error :messages="$errors->get('rank')" class="mt-1" />
            </div>
            <div>
                <label class="form-label">Points <span class="text-red-500">*</span></label>
                <input type="number" name="points" min="0" value="{{ old('points', $isEdit ? $entry->points : '') }}" required class="form-input" placeholder="e.g. 4200">
                <x-input-error :messages="$errors->get('points')" class="mt-1" />
            </div>
        </div>

        <div>
            <label class="form-label">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $isEdit ? $entry->name : '') }}" required class="form-input" placeholder="e.g. Rahul Sharma">
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div>
            <label class="form-label">Role / Title <span class="font-normal text-slate-400">(optional)</span></label>
            <input type="text" name="description" value="{{ old('description', $isEdit ? $entry->description : '') }}" class="form-input" placeholder="e.g. Sales Champion, Top Performer, Rising Star">
            <x-input-error :messages="$errors->get('description')" class="mt-1" />
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label class="form-label">Period Start <span class="font-normal text-slate-400">(optional)</span></label>
                <input type="date" name="period_start" value="{{ old('period_start', $isEdit && $entry->period_start ? $entry->period_start->format('Y-m-d') : '') }}" class="form-input">
                <x-input-error :messages="$errors->get('period_start')" class="mt-1" />
            </div>
            <div>
                <label class="form-label">Period End <span class="font-normal text-slate-400">(optional)</span></label>
                <input type="date" name="period_end" value="{{ old('period_end', $isEdit && $entry->period_end ? $entry->period_end->format('Y-m-d') : '') }}" class="form-input">
                <x-input-error :messages="$errors->get('period_end')" class="mt-1" />
            </div>
            <p class="sm:col-span-2 -mt-2 text-xs text-slate-400">e.g. 1 Aug – 31 Aug, to tag which month/period this entry belongs to. Leave blank if this entry isn't tied to a specific period.</p>
        </div>

        <div>
            <label class="form-label">Photo <span class="font-normal text-slate-400">(optional)</span></label>
            @if ($isEdit && $entry->imageUrl())
                <img src="{{ $entry->imageUrl() }}" alt="" class="mb-2 h-16 w-16 rounded-full object-cover">
            @endif
            <input type="file" name="image" accept="image/*" class="form-input">
            <x-input-error :messages="$errors->get('image')" class="mt-1" />
        </div>
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Save Changes' : 'Add Entry' }}</button>
    </div>
</form>
