<x-layout title="{{ $course->title }}">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('user.courses.index') }}" class="hover:text-primary">Courses</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $course->title }}</span>
    </nav>

    <div class="mt-3 flex items-start gap-4">
        @if ($course->thumbnailUrl())
            <img src="{{ $course->thumbnailUrl() }}" alt="" class="h-12 w-12 shrink-0 rounded-lg object-cover">
        @else
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                <x-icon name="video" class="w-6 h-6" />
            </span>
        @endif
        <div>
            <h1 class="text-xl font-semibold text-secondary-dark">{{ $course->title }}</h1>
        </div>
    </div>

    @if ($completed)
        <div class="mt-4 rounded-xl border border-success/30 bg-success-light p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-success">
                    <x-icon name="check" class="w-5 h-5" />
                    <h2 class="font-semibold">Course completed</h2>
                </div>
                @if ($certificate)
                    <a href="{{ route('user.certificates.show', $certificate) }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-success px-3 py-1.5 text-xs font-medium text-white hover:opacity-90">
                        <x-icon name="badge" class="w-3.5 h-3.5" />
                        View Certificate
                    </a>
                @endif
            </div>
            @if (! is_null($score->percent))
                <p class="mt-1 text-sm text-secondary-dark">
                    {{ $score->pending_count > 0 ? 'Score so far' : 'Final score' }}: {{ $score->percent }}%
                    ({{ $score->earned_points }}/{{ $score->graded_points }} marks, {{ $score->correct_count }}/{{ $score->graded_count }} correct)
                    @if ($score->pending_count > 0)
                        &mdash; {{ $score->pending_count }} {{ Str::plural('answer', $score->pending_count) }} pending manual review.
                    @endif
                </p>
            @else
                <p class="mt-1 text-sm text-secondary-dark">No scored questions in this course.</p>
            @endif
        </div>
    @endif

    @include('user.courses._player')
</x-layout>
