<x-layout title="Events & Webinars" title-icon="calendar" subtitle="Training sessions and webinars salespeople can register for">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="flex w-full flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative w-full sm:max-w-sm">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events..." class="form-input pl-10">
            </div>

            <select name="status" class="form-input w-full sm:w-48" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="published" @selected(request('status') === 'published')>Published</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
            </select>

            @if (request('search') || request('status'))
                <a href="{{ route('admin.events.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Reset</a>
            @endif
        </form>

        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-event')" class="btn-primary shrink-0">
            <x-icon name="plus" class="h-4 w-4" />
            Add Event
        </button>
    </div>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">All Events</h2>
            <p class="mt-0.5 text-xs text-slate-400">{{ $events->total() }} total event{{ $events->total() === 1 ? '' : 's' }}</p>
        </div>

        @if ($events->count())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-400">
                            <th class="px-5 py-3 font-semibold">Event</th>
                            <th class="px-5 py-3 font-semibold">Date &amp; Time</th>
                            <th class="px-5 py-3 font-semibold">Location</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold">Registrants</th>
                            <th class="px-5 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($events as $event)
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-11 w-11 shrink-0 flex-col items-center justify-center rounded-lg bg-brand-50 leading-none text-brand-700">
                                            <span class="text-sm font-extrabold">{{ $event->starts_at->format('d') }}</span>
                                            <span class="text-[10px] font-semibold uppercase">{{ $event->starts_at->format('M') }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-slate-800">{{ $event->title }}</p>
                                            @if ($event->subtitle)
                                                <p class="mt-0.5 max-w-xs truncate text-xs text-slate-400">{{ $event->subtitle }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    {{ $event->starts_at->format('h:i A') }}
                                    @if ($event->ends_at)
                                        &ndash; {{ $event->ends_at->format('h:i A') }}
                                    @endif
                                    @if ($event->hasEnded())
                                        <span class="badge badge-slate ml-1">Ended</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    @if ($event->location)
                                        <span class="inline-flex items-center gap-1.5 text-slate-600">
                                            <x-icon name="map-pin" class="h-4 w-4 shrink-0 text-slate-400" />
                                            <span class="max-w-[160px] truncate">{{ $event->location }}</span>
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    @if ($event->is_published)
                                        <span class="badge badge-green">
                                            <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                            Published
                                        </span>
                                    @else
                                        <span class="badge badge-slate">
                                            <x-icon name="document" class="h-3.5 w-3.5" />
                                            Draft
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.events.registrants', $event) }}" class="inline-flex items-center gap-1.5 font-semibold text-brand-700 hover:underline">
                                        <x-icon name="users" class="h-4 w-4" />
                                        {{ $event->registrations_count }}
                                    </a>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-event-{{ $event->id }}')" title="Edit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-violet-200 bg-violet-50 text-violet-600 transition hover:bg-violet-100">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                        </button>

                                        <form method="POST" action="{{ route('admin.events.publish.toggle', $event) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_published" value="{{ $event->is_published ? '0' : '1' }}">
                                            <button type="submit" title="{{ $event->is_published ? 'Move to Draft' : 'Publish' }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-green-200 bg-green-50 text-green-600 transition hover:bg-green-100">
                                                <x-icon name="{{ $event->is_published ? 'eye-off' : 'check-circle' }}" class="h-4 w-4" />
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.events.destroy', $event) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete \'{{ $event->title }}\'? This also removes everyone\'s registrations for it.', target: $el })">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100">
                                                <x-icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <x-modal name="edit-event-{{ $event->id }}" :show="false" max-width="lg">
                                @include('admin.events._form', ['event' => $event])
                            </x-modal>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($events->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $events->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                    <x-icon name="calendar" class="h-7 w-7 text-brand-600" />
                </div>
                <h3 class="mt-4 font-bold text-slate-800">No events found</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">
                    @if (request('search') || request('status'))
                        Try changing your search or filters.
                    @else
                        Click "Add Event" to create the first one.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <x-modal name="add-event" :show="$errors->isNotEmpty()" max-width="lg">
        @include('admin.events._form', ['event' => null])
    </x-modal>

</x-layout>
