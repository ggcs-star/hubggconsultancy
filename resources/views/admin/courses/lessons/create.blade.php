<x-layout title="Add Lesson">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.courses.index') }}" class="hover:text-primary">Training / LMS</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <a href="{{ route('admin.courses.show', ['course' => $courseModule->course, 'tab' => 'modules']) }}" class="hover:text-primary">{{ $courseModule->course->title }}</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">Add Lesson</span>
    </nav>

    <h1 class="mt-3 text-xl font-semibold text-secondary-dark">Add Lesson to "{{ $courseModule->title }}"</h1>
    <p class="text-sm text-secondary">You'll be able to add quiz checkpoints once the lesson is created.</p>

    <div class="mt-6 max-w-2xl rounded-xl border border-app-border bg-white p-6">
        <form method="POST" action="{{ route('admin.course-lessons.store', $courseModule) }}" enctype="multipart/form-data"
            x-data="{ source: '{{ old('video_source', 'upload') }}', uploading: false }"
            x-on:submit="uploading = true" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="title" value="Lesson Title *" class="uppercase text-xs tracking-wide" />
                <x-text-input id="title" name="title" class="mt-1.5" :value="old('title')" required autofocus />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="description" value="Description" class="uppercase text-xs tracking-wide" />
                <textarea id="description" name="description" rows="3"
                    class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            @include('admin.courses.lessons._video-fields')

            <div>
                <x-input-label for="duration" value="Duration (display label, e.g. 2:30)" class="uppercase text-xs tracking-wide" />
                <x-text-input id="duration" name="duration" class="mt-1.5" :value="old('duration')" placeholder="2:30" />
                <x-input-error :messages="$errors->get('duration')" class="mt-2" />
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.courses.show', ['course' => $courseModule->course, 'tab' => 'modules']) }}">
                    <x-secondary-button type="button">Cancel</x-secondary-button>
                </a>
                <x-primary-button x-bind:disabled="uploading">
                    <span x-show="!uploading">Create Lesson</span>
                    <span x-show="uploading" x-cloak>Uploading&hellip; please wait, this can take a while for large videos.</span>
                </x-primary-button>
            </div>
        </form>
    </div>
</x-layout>
