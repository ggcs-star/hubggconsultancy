<x-layout title="Calendar" title-icon="calendar" subtitle="Your personal tasks and schedule">

    @php
        $events = $tasks->map(fn ($task) => [
            'id' => $task->id,
            'title' => $task->title,
            'start' => $task->time ? $task->date->format('Y-m-d') . 'T' . $task->time : $task->date->format('Y-m-d'),
            'allDay' => ! $task->time,
            'color' => '#7c3aed',
        ])->values();

        $tasksById = $tasks->keyBy('id')->map(fn ($task) => [
            'id' => $task->id,
            'title' => $task->title,
            'date' => $task->date->format('Y-m-d'),
            'time' => $task->time ? \Illuminate\Support\Carbon::parse($task->time)->format('H:i') : '',
        ]);
    @endphp

    <div
        x-data="{
            mode: 'add',
            taskId: null,
            title: '',
            date: '{{ now()->toDateString() }}',
            time: '',
            selectedIds: [],
            tasksById: {{ Illuminate\Support\Js::from($tasksById) }},
            openAdd(date) {
                this.mode = 'add';
                this.taskId = null;
                this.title = '';
                this.date = date || '{{ now()->toDateString() }}';
                this.time = '';
                $dispatch('open-modal', 'task-form');
            },
            openEdit(id) {
                const task = this.tasksById[id];
                if (! task) return;
                this.mode = 'edit';
                this.taskId = task.id;
                this.title = task.title;
                this.date = task.date;
                this.time = task.time;
                $dispatch('open-modal', 'task-form');
            },
        }"
        x-on:open-add-task.window="openAdd($event.detail)"
        x-on:open-edit-task.window="openEdit($event.detail)"
    >

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            <div class="card min-w-0 p-5 lg:col-span-3">
                <div id="task-calendar" class="w-full"></div>
            </div>

            <div class="card flex flex-col p-5 lg:col-span-1">
                <form
                    id="bulk-delete-tasks-form"
                    method="POST"
                    action="{{ route('tasks.bulk-destroy') }}"
                    x-on:submit.prevent="$dispatch('confirm-action', { message: `Delete ${selectedIds.length} selected task(s)?`, target: $el })"
                >
                    @csrf
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="task_ids[]" :value="id">
                    </template>
                </form>

                <div class="flex items-center justify-between">
                    <p class="font-bold text-slate-800">To Do List</p>
                    <div class="flex items-center gap-2">
                        <button
                            type="submit"
                            form="bulk-delete-tasks-form"
                            title="Delete Selected"
                            x-show="selectedIds.length > 0"
                            x-cloak
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100"
                        >
                            <x-icon name="trash" class="h-4 w-4" />
                        </button>
                        <button type="button" x-on:click="openAdd()" title="Add Task" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-700 text-white transition hover:bg-brand-800">
                            <x-icon name="plus" class="h-5 w-5" />
                        </button>
                    </div>
                </div>

                <div class="mt-4 flex-1 space-y-1 overflow-y-auto">
                    @forelse ($todoList as $task)
                        <div class="flex items-center gap-3 rounded-lg px-2 py-2 hover:bg-slate-50">
                            <input type="checkbox" x-model="selectedIds" value="{{ $task->id }}" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-amber-600">{{ $task->title }}</p>
                                <p class="text-xs text-slate-400">{{ $task->dateLabel() }}@if ($task->timeLabel()) &middot; {{ $task->timeLabel() }} @endif</p>
                            </div>

                            <button type="button" x-on:click="openEdit({{ $task->id }})" title="Edit" class="shrink-0 text-slate-400 hover:text-brand-700">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </button>

                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete this task?', target: $el })">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete" class="shrink-0 text-slate-400 hover:text-red-600">
                                    <x-icon name="x" class="h-4 w-4" />
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="px-2 py-8 text-center text-sm text-slate-400">No tasks yet. Click "+" to add one.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <x-modal name="task-form" max-width="md">
            <form
                method="POST"
                :action="mode === 'edit' ? `{{ url('/tasks') }}/${taskId}` : '{{ route('tasks.store') }}'"
                class="flex flex-col"
            >
                @csrf
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h2 class="text-lg font-bold text-slate-800" x-text="mode === 'edit' ? 'Edit Task' : 'Add Task'"></h2>
                    <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
                        <x-icon name="x" class="h-5 w-5" />
                    </button>
                </div>

                <div class="space-y-5 px-6 py-6">
                    <div>
                        <label class="form-label">Title</label>
                        <input type="text" name="title" x-model="title" required placeholder="e.g. Lead generation" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Date</label>
                        <input type="date" name="date" x-model="date" required class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Time <span class="font-normal text-slate-400">(optional)</span></label>
                        <input type="time" name="time" x-model="time" class="form-input">
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
                    <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </form>
        </x-modal>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calendarEl = document.getElementById('task-calendar');

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay',
                    },
                    height: 'auto',
                    handleWindowResize: true,
                    events: {{ Illuminate\Support\Js::from($events) }},
                    dateClick: function (info) {
                        window.dispatchEvent(new CustomEvent('open-add-task', { detail: info.dateStr }));
                    },
                    eventClick: function (info) {
                        window.dispatchEvent(new CustomEvent('open-edit-task', { detail: Number(info.event.id) }));
                    },
                });

                calendar.render();

                // The calendar can be measured before the surrounding grid/sidebar
                // layout has finished settling on first paint — force a couple more
                // measurements shortly after everything is actually laid out.
                requestAnimationFrame(() => calendar.updateSize());
                setTimeout(() => calendar.updateSize(), 250);
            });
        </script>
    @endpush

</x-layout>
