<x-layout :title="$course->title . ' — Preview'">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.courses.index') }}" class="hover:text-primary">Training / LMS</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <a href="{{ route('admin.courses.show', $course) }}" class="hover:text-primary">{{ $course->title }}</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">Preview</span>
    </nav>

    <div class="mt-3 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-secondary-dark">{{ $course->title }} — Preview</h1>
            <p class="mt-1 text-sm text-secondary">This is exactly what an assigned client sees, lessons and quizzes included — nothing you do here is saved.</p>
        </div>
        <a href="{{ route('admin.courses.show', ['course' => $course, 'tab' => 'modules']) }}"
            class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-app-border bg-white px-4 py-2 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
            <x-icon name="edit" class="w-4 h-4" />
            Edit Course
        </a>
    </div>

    @include('user.courses._player')
</x-layout>
