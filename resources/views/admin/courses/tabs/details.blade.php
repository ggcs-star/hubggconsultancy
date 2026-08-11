<div class="max-w-2xl rounded-xl border border-app-border bg-white p-6">
    <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="title" value="Course Title *" class="uppercase text-xs tracking-wide" />
            <x-text-input id="title" name="title" class="mt-1.5" :value="old('title', $course->title)" placeholder="Enter course Title" required />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="description" value="Description" class="uppercase text-xs tracking-wide" />
            <textarea id="description" name="description" rows="4"
                class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('description', $course->description) }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <div>
            <x-input-label value="Thumbnail" class="uppercase text-xs tracking-wide" />
            @if ($course->thumbnailUrl())
                <img src="{{ $course->thumbnailUrl() }}" alt="" class="mt-2 h-24 w-40 rounded-lg object-cover border border-app-border">
            @endif
            <input name="thumbnail" type="file" accept="image/*"
                class="mt-2 w-full rounded-lg border-app-border text-sm shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-surface-alt file:px-3 file:py-1.5 file:text-sm" />
            <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
        </div>

        <div class="flex justify-end">
            <x-primary-button>Save Changes</x-primary-button>
        </div>
    </form>
</div>
