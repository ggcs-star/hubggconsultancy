@php
    $modules = $course->modules;
@endphp

<div class="flex items-center justify-between">
    <div>
        <h2 class="text-lg font-semibold text-secondary-dark">Modules & Lessons</h2>
        <p class="text-sm text-secondary">Group lessons into modules — each lesson can have a video and quiz checkpoints.</p>
    </div>
    <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'add-module')"
        class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-primary/30 bg-primary-light px-4 py-2.5 text-sm font-medium text-primary hover:border-primary/60 hover:bg-primary/20">
        <x-icon name="plus" class="w-4 h-4" />
        Add Module
    </button>
</div>

<div class="mt-6 space-y-4" data-sortable data-sortable-url="{{ route('admin.course-modules.reorder', $course) }}">
    @forelse ($modules as $module)
        <div x-data="{ open: true }" data-sortable-item draggable="true" data-sortable-id="{{ $module->id }}" class="cursor-move select-none rounded-xl border border-app-border bg-white" title="Drag to reorder">
            <div class="flex items-center justify-between gap-3 px-4 py-3">
                <x-icon name="grip" class="w-4 h-4 shrink-0 text-secondary/60" />
                <button type="button" x-on:click="open = !open" class="flex flex-1 items-center gap-2 text-left">
                    <x-icon name="chevron-down" class="w-4 h-4 text-secondary transition-transform" x-bind:class="!open && '-rotate-90'" />
                    <span class="font-semibold text-secondary-dark">{{ $module->title }}</span>
                    <span class="text-xs text-secondary">{{ $module->lessons->count() }} lessons</span>
                </button>

                <div x-data="{ menuOpen: false }" class="relative" x-on:click.outside="menuOpen = false">
                    <button type="button" x-on:click.stop="menuOpen = !menuOpen"
                        class="rounded-md p-1.5 text-secondary hover:bg-surface-alt hover:text-secondary-dark" title="More options">
                        <x-icon name="more-vertical" class="w-4 h-4" />
                    </button>
                    <div x-show="menuOpen" x-cloak class="absolute right-0 z-10 mt-1 w-52 rounded-lg border border-app-border bg-white py-1 shadow-lg">
                        <button type="button" x-on:click="menuOpen = false; $dispatch('open-modal', 'edit-module-{{ $module->id }}')"
                            class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-secondary-dark hover:bg-surface-alt">
                            <x-icon name="edit" class="w-4 h-4" />
                            Edit Module
                        </button>
                        <form method="POST" action="{{ route('admin.course-modules.destroy', $module) }}"
                            x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete this module and all of its lessons?', target: $el })">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-danger hover:bg-danger-light">
                                <x-icon name="trash" class="w-4 h-4" />
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div x-show="open" x-cloak class="divide-y divide-app-border border-t border-app-border">
                @if ($module->description)
                    <p class="px-4 py-3 text-sm text-secondary">{{ $module->description }}</p>
                @endif

                <div data-sortable data-sortable-url="{{ route('admin.course-modules.items.reorder', $module) }}">
                @forelse ($module->orderedItems() as $item)
                    @if ($item->item_type === 'lesson')
                        @php $lesson = $item; @endphp
                        <div data-sortable-item draggable="true" data-sortable-id="lesson-{{ $lesson->id }}" class="cursor-move select-none flex items-center justify-between gap-3 px-4 py-2.5 pl-11" title="Drag to reorder">
                            <span class="flex items-center gap-2 text-sm text-secondary-dark">
                                <x-icon name="grip" class="w-3.5 h-3.5 shrink-0 text-secondary/60" />
                                @if ($lesson->thumbnail)
                                    <img src="{{ $lesson->thumbnailUrl() }}" alt="" class="h-7 w-11 shrink-0 rounded object-cover">
                                @else
                                    <x-icon name="{{ $lesson->video_source === 'youtube' ? 'video' : 'play-circle' }}" class="w-4 h-4 text-secondary" />
                                @endif
                                {{ $lesson->title }}
                                <span class="text-xs text-secondary">
                                    {{ $lesson->checkpoints->count() }} {{ Str::plural('checkpoint', $lesson->checkpoints->count()) }},
                                    {{ $lesson->checkpoints->sum(fn ($checkpoint) => $checkpoint->questions->count()) }} {{ Str::plural('question', $lesson->checkpoints->sum(fn ($checkpoint) => $checkpoint->questions->count())) }}
                                </span>
                            </span>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.courses.preview', $course) }}?item=lesson-{{ $lesson->id }}" target="_blank"
                                    class="rounded-md p-1.5 text-secondary hover:bg-surface-alt hover:text-secondary-dark" title="View lesson">
                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                </a>
                                <a href="{{ route('admin.course-lessons.edit', $lesson) }}"
                                    class="rounded-md p-1.5 text-secondary hover:bg-surface-alt hover:text-secondary-dark" title="Edit lesson">
                                    <x-icon name="edit" class="w-3.5 h-3.5" />
                                </a>
                                <form method="POST" action="{{ route('admin.course-lessons.destroy', $lesson) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete this lesson?', target: $el })">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md p-1.5 text-secondary hover:bg-danger-light hover:text-danger" title="Delete lesson">
                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        @php $quiz = $item; @endphp
                        <div data-sortable-item draggable="true" data-sortable-id="quiz-{{ $quiz->id }}" class="cursor-move select-none flex items-center justify-between gap-3 bg-warning-light/40 px-4 py-2.5 pl-11" title="Drag to reorder">
                            <span class="flex items-center gap-2 text-sm text-secondary-dark">
                                <x-icon name="grip" class="w-3.5 h-3.5 shrink-0 text-secondary/60" />
                                <x-icon name="help-circle" class="w-4 h-4 text-warning" />
                                {{ $quiz->title }}
                                <x-badge :classes="$quiz->is_required ? 'bg-warning-light text-warning' : 'bg-surface-alt text-secondary'">
                                    {{ $quiz->is_required ? 'Required' : 'Optional' }}
                                </x-badge>
                                <span class="text-xs text-secondary">{{ $quiz->questions->count() }} {{ Str::plural('question', $quiz->questions->count()) }}, {{ $quiz->questions->sum('points') }} {{ Str::plural('pt', $quiz->questions->sum('points')) }}</span>
                            </span>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.courses.preview', $course) }}?item=quiz-{{ $quiz->id }}" target="_blank"
                                    class="rounded-md p-1.5 text-secondary hover:bg-surface-alt hover:text-secondary-dark" title="View quiz">
                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                </a>
                                <a href="{{ route('admin.course-module-quizzes.edit', $quiz) }}"
                                    class="rounded-md p-1.5 text-secondary hover:bg-surface-alt hover:text-secondary-dark" title="Manage quiz">
                                    <x-icon name="edit" class="w-3.5 h-3.5" />
                                </a>
                                <form method="POST" action="{{ route('admin.course-module-quizzes.destroy', $quiz) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete this quiz and its questions?', target: $el })">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md p-1.5 text-secondary hover:bg-danger-light hover:text-danger" title="Delete quiz">
                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @empty
                    <p class="px-4 py-3 pl-11 text-xs text-secondary">No lessons yet.</p>
                @endforelse
                </div>

                <div class="flex items-center gap-4 px-4 py-2.5 pl-11">
                    <a href="{{ route('admin.course-lessons.create', $module) }}"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:underline">
                        <x-icon name="plus" class="w-3.5 h-3.5" />
                        Add Lesson
                    </a>
                    <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'add-quiz-{{ $module->id }}')"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-warning hover:underline">
                        <x-icon name="plus" class="w-3.5 h-3.5" />
                        Add Module Quiz
                    </button>
                </div>
            </div>
        </div>

        <x-modal name="edit-module-{{ $module->id }}" maxWidth="md">
            <form method="POST" action="{{ route('admin.course-modules.update', $module) }}" class="p-6">
                @csrf
                @method('PUT')
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-secondary-dark">Edit Module</h2>
                    <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>
                <div class="mt-6 space-y-5">
                    <div>
                        <x-input-label value="Title *" class="text-xs uppercase tracking-wide" />
                        <x-text-input name="title" class="mt-1.5" :value="$module->title" required />
                    </div>
                    <div>
                        <x-input-label value="Description" class="text-xs uppercase tracking-wide" />
                        <textarea name="description" rows="2" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ $module->description }}</textarea>
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                    <x-primary-button>Save</x-primary-button>
                </div>
            </form>
        </x-modal>

        <x-modal name="add-quiz-{{ $module->id }}" maxWidth="sm">
            @include('admin.courses.modules._quiz-form', ['module' => $module, 'quiz' => null])
        </x-modal>
    @empty
        <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
            No modules yet. Click "Add Module" to create your first one.
        </div>
    @endforelse
</div>

<x-modal name="add-module" maxWidth="md">
    <form method="POST" action="{{ route('admin.course-modules.store', $course) }}" class="p-6">
        @csrf
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-secondary-dark">Add Module</h2>
            <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>
        <div class="mt-6 space-y-5">
            <div>
                <x-input-label value="Title *" class="text-xs uppercase tracking-wide" />
                <x-text-input name="title" class="mt-1.5" required autofocus />
            </div>
            <div>
                <x-input-label value="Description" class="text-xs uppercase tracking-wide" />
                <textarea name="description" rows="2" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"></textarea>
            </div>
        </div>
        <div class="mt-8 flex justify-end gap-3">
            <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
            <x-primary-button>Add Module</x-primary-button>
        </div>
    </form>
</x-modal>
