<x-layout title="Courses">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Courses</h1>
        <p class="mt-1 text-sm text-secondary">Short videos with quizzes along the way</p>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($courses as $course)
            @php $score = $course->score; @endphp
            <a href="{{ route('user.courses.show', $course) }}" class="overflow-hidden rounded-xl border border-app-border bg-white hover:border-primary/40 hover:shadow-sm">
                @if ($course->thumbnailUrl())
                    <img src="{{ $course->thumbnailUrl() }}" alt="" class="aspect-video w-full object-cover">
                @else
                    <div class="flex aspect-video w-full items-center justify-center bg-primary-light text-primary">
                        <x-icon name="video" class="w-8 h-8" />
                    </div>
                @endif
                <div class="p-5">
                    <h2 class="truncate font-semibold text-secondary-dark">{{ $course->title }}</h2>
                    <p class="text-xs text-secondary">{{ $course->lessons_count }} {{ Str::plural('lesson', $course->lessons_count) }}</p>
                    @if ($course->description)
                        <p class="mt-3 line-clamp-2 text-sm text-secondary">{{ $course->description }}</p>
                    @endif
                    @if (! is_null($score->percent))
                        <div class="mt-4">
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-surface-alt">
                                <div class="h-full rounded-full bg-success" style="width: {{ $score->percent }}%"></div>
                            </div>
                            <p class="mt-1.5 text-xs text-secondary">Score: {{ $score->percent }}%</p>
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                No courses have been assigned to you yet.
            </div>
        @endforelse
    </div>
</x-layout>
