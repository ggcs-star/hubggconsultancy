@php
    $isEdit = (bool) $resource;
@endphp

<form method="POST"
    action="{{ $isEdit ? route('admin.resources.update', $resource) : route('admin.resources.store') }}"
    enctype="multipart/form-data"
    class="p-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Resource' : 'Add Resource' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <div class="mt-6 space-y-5">
        <div>
            <label class="form-label">Title</label>
            <input type="text" name="title" value="{{ old('title', $isEdit ? $resource->title : '') }}" required placeholder="e.g. ₹5 Lakh Se Kya Hota Hai?" class="form-input">
        </div>

        <div>
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-input" placeholder="Shown on the resource card, with a See More toggle">{{ old('description', $isEdit ? $resource->description : '') }}</textarea>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label class="form-label">Hindi Thumbnail</label>
                @if ($isEdit && $resource->hindi_thumbnail)
                    <img src="{{ asset('storage/' . $resource->hindi_thumbnail) }}" alt="" class="mb-2 h-24 w-full rounded-lg object-cover">
                @endif
                <input type="file" name="hindi_thumbnail" accept="image/*" class="form-input">
                <x-input-error :messages="$errors->get('hindi_thumbnail')" class="mt-1" />
            </div>

            <div>
                <label class="form-label">English Thumbnail</label>
                @if ($isEdit && $resource->english_thumbnail)
                    <img src="{{ asset('storage/' . $resource->english_thumbnail) }}" alt="" class="mb-2 h-24 w-full rounded-lg object-cover">
                @endif
                <input type="file" name="english_thumbnail" accept="image/*" class="form-input">
                <x-input-error :messages="$errors->get('english_thumbnail')" class="mt-1" />
            </div>
        </div>
        <p class="-mt-3 text-xs text-slate-400">Shown on the resource card depending on which language tab a learner has selected. If a language's thumbnail is left blank, the other language's is used instead.</p>

        <div>
            <label class="form-label">Hindi YouTube Link</label>
            <input type="text" name="hindi_youtube_url" value="{{ old('hindi_youtube_url', $isEdit ? $resource->hindi_youtube_url : '') }}" class="form-input" placeholder="https://www.youtube.com/watch?v=...">
            <x-input-error :messages="$errors->get('hindi_youtube_url')" class="mt-1" />
        </div>

        <div>
            <label class="form-label">English YouTube Link</label>
            <input type="text" name="english_youtube_url" value="{{ old('english_youtube_url', $isEdit ? $resource->english_youtube_url : '') }}" class="form-input" placeholder="https://www.youtube.com/watch?v=...">
            <x-input-error :messages="$errors->get('english_youtube_url')" class="mt-1" />
        </div>

        <p class="text-xs text-slate-400">Playlist links are supported too — they just open on YouTube directly, with no in-page checkpoint quiz (a single video is needed to track watch time).</p>
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Save Changes' : 'Add Resource' }}</button>
    </div>
</form>
