<x-layout title="Courses">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Courses</h1>
            <p class="mt-1 text-sm text-secondary">
                Build courses — modules, lessons, and quiz checkpoints trainees complete for a score
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.course-quiz-answers.pending') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-app-border bg-white px-4 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="help-circle" class="w-4 h-4" />
                Pending Reviews
                @if ($pendingReviewCount > 0)
                    <span class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-danger px-1.5 text-xs font-semibold text-white">
                        {{ $pendingReviewCount }}
                    </span>
                @endif
            </a>
            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-course')">
                <x-icon name="plus" class="w-4 h-4" />
                Add Course
            </x-primary-button>
        </div>
    </div>

    <form method="GET" class="mt-6 rounded-xl border border-app-border bg-white p-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-12">
            <div class="min-w-0 lg:col-span-5" x-data="{ search: @js(request('search', '')) }">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Search Course</label>
                <input type="text" name="search" x-model="search" placeholder="Search by title..."
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
            </div>

            <div class="min-w-0 lg:col-span-7">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Status</label>
                <select name="status" onchange="this.form.submit()"
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">All Statuses</option>
                    <option value="published" @selected(request('status') === 'published')>Published</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                </select>
            </div>
        </div>

        <button type="submit" class="hidden"></button>

        @if (request()->anyFilled(['search', 'status']))
            <div class="mt-4">
                <a href="{{ route('admin.courses.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-primary/30 px-4 py-2 text-sm font-medium text-primary hover:bg-primary-light">
                    <x-icon name="refresh-cw" class="w-4 h-4" />
                    Reset Filters
                </a>
            </div>
        @endif
    </form>

    <div class="mt-6 overflow-x-auto rounded-xl border border-app-border bg-white">
        <table class="min-w-full divide-y divide-app-border text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                    <th class="px-4 py-3">Course</th>
                    <th class="px-4 py-3">Modules</th>
                    <th class="px-4 py-3">Lessons</th>
                    <th class="px-4 py-3">Quizzes</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-app-border">
                @forelse ($courses as $course)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.courses.show', $course) }}" class="flex items-center gap-3 hover:text-primary">
                                @if ($course->thumbnailUrl())
                                    <img src="{{ $course->thumbnailUrl() }}" alt="" class="h-10 w-10 shrink-0 rounded-lg object-cover">
                                @else
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                                        <x-icon name="video" class="w-5 h-5" />
                                    </span>
                                @endif
                                <span class="font-medium text-secondary-dark">{{ $course->title }}</span>
                            </a>
                        </td>
                        <td class="px-4 py-3 font-medium text-secondary-dark">{{ $course->modules_count }}</td>
                        <td class="px-4 py-3 font-medium text-secondary-dark">{{ $course->lessons_count }}</td>
                        <td class="px-4 py-3 font-medium text-secondary-dark">{{ $course->checkpoints_count }}</td>
                        <td class="px-4 py-3">
                            @php
                                $courseStatusColorClass = $course->is_published ? 'text-success' : 'text-secondary-dark';
                                $courseStatusSelectClasses = $course->is_published ? 'bg-success-light text-success' : 'bg-secondary-light text-secondary-dark';
                            @endphp
                            <form method="POST" action="{{ route('admin.courses.publish.toggle', $course) }}">
                                @csrf
                                @method('PATCH')
                                <div class="relative inline-block">
                                    <span class="pointer-events-none absolute left-3 top-1/2 h-1.5 w-1.5 -translate-y-1/2 rounded-full bg-current {{ $courseStatusColorClass }}"></span>
                                    <select name="is_published" onchange="this.form.submit()"
                                        class="appearance-none bg-none rounded-full border-0 py-1.5 pl-7 pr-7 text-xs font-medium focus:ring-2 focus:ring-primary {{ $courseStatusSelectClasses }}">
                                        <option value="1" @selected($course->is_published)>Published</option>
                                        <option value="0" @selected(! $course->is_published)>Draft</option>
                                    </select>
                                    <x-icon name="chevron-down"
                                        class="pointer-events-none absolute right-2.5 top-1/2 h-3 w-3 -translate-y-1/2 {{ $courseStatusColorClass }}" />
                                </div>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap items-center justify-end gap-1.5">
                                <a href="{{ route('admin.courses.show', $course) }}" title="Manage Course"
                                    class="inline-flex items-center justify-center rounded-md border border-warning/30 bg-warning-light p-1.5 text-warning hover:border-warning/60 hover:bg-warning/20">
                                    <x-icon name="edit" class="w-3.5 h-3.5" />
                                </a>
                                <a href="{{ route('admin.courses.preview', $course) }}" title="Preview as Client" target="_blank"
                                    class="inline-flex items-center justify-center rounded-md border border-chart-4/30 bg-chart-4/15 p-1.5 text-chart-4 hover:border-chart-4/60 hover:bg-chart-4/25">
                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                </a>
                                <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                                    x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete this course and everything under it?', target: $el })">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete Course"
                                        class="inline-flex items-center justify-center rounded-md border border-danger/30 bg-danger-light p-1.5 text-danger hover:border-danger/60 hover:bg-danger/20">
                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-secondary">
                            @if (request()->anyFilled(['search', 'status']))
                                No courses match your filters.
                            @else
                                No courses yet. Click "Add Course" to create your first one.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $courses->links() }}
    </div>

    <x-modal name="add-course" :show="$errors->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('admin.courses.store') }}" class="p-6" enctype="multipart/form-data">
            @csrf

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-secondary-dark">Add Course</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <div class="mt-6 space-y-5">
                <div>
                    <x-input-label for="title" value="Course Title *" class="uppercase text-xs tracking-wide" />
                    <x-text-input id="title" name="title" class="mt-1.5" :value="old('title')" placeholder="Enter course Title" required autofocus />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" value="Description" class="uppercase text-xs tracking-wide" />
                    <textarea id="description" name="description" rows="3" placeholder="What will clients learn in this course?"
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="thumbnail" value="Thumbnail" class="uppercase text-xs tracking-wide" />
                    <input id="thumbnail" name="thumbnail" type="file" accept="image/*"
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-surface-alt file:px-3 file:py-1.5 file:text-sm" />
                    <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button>Create Course</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-layout>
