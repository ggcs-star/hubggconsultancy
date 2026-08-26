<x-layout title="Events & Webinars" title-icon="calendar" subtitle="Register for upcoming training sessions and webinars">

    <div class="card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Upcoming Training / Webinars</h2>
        </div>

        @if ($upcoming->isEmpty())
            <div class="px-6 py-12 text-center text-sm text-slate-400">No upcoming events right now — check back soon.</div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($upcoming as $event)
                    @php $registered = $event->isRegisteredBy($user); @endphp
                    <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4">
                        <div class="flex items-center gap-4">
                            <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-xl bg-brand-50 leading-none text-brand-700">
                                <span class="text-lg font-extrabold">{{ $event->starts_at->format('d') }}</span>
                                <span class="text-[10px] font-semibold uppercase">{{ $event->starts_at->format('M') }}</span>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $event->title }}</p>
                                @if ($event->subtitle)
                                    <p class="text-sm text-slate-500">{{ $event->subtitle }}</p>
                                @endif
                                <p class="mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-400">
                                    <span class="flex items-center gap-1">
                                        <x-icon name="clock" class="h-3.5 w-3.5" />
                                        {{ $event->starts_at->format('h:i A') }}@if ($event->ends_at) &ndash; {{ $event->ends_at->format('h:i A') }}@endif
                                    </span>
                                    @if ($event->location)
                                        <span class="flex items-center gap-1">
                                            <x-icon name="map-pin" class="h-3.5 w-3.5" />
                                            {{ $event->location }}
                                        </span>
                                    @endif
                                    <span class="flex items-center gap-1">
                                        <x-icon name="users" class="h-3.5 w-3.5" />
                                        {{ $event->registrations->count() }} {{ Str::plural('person', $event->registrations->count()) }} registered
                                    </span>
                                </p>
                            </div>
                        </div>

                        @if ($registered)
                            <form
                                method="POST"
                                action="{{ route('user.events.unregister', $event) }}"
                                class="group shrink-0"
                                x-data=""
                                x-on:submit.prevent="$dispatch('confirm-action', { message: 'Cancel your registration for \'{{ $event->title }}\'? You can register again later if you change your mind.', target: $el })"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    title="Can't make it? Click to cancel your registration."
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 transition group-hover:border-red-200 group-hover:bg-red-50 group-hover:text-red-600"
                                >
                                    <x-icon name="check-circle" class="h-4 w-4 group-hover:hidden" />
                                    <x-icon name="x" class="hidden h-4 w-4 group-hover:block" />
                                    <span class="group-hover:hidden">Registered</span>
                                    <span class="hidden group-hover:inline">Cancel Registration</span>
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('user.events.register', $event) }}" class="shrink-0">
                                @csrf
                                <button type="submit" class="rounded-xl border border-brand-200 bg-white px-5 py-2 text-sm font-semibold text-brand-700 transition hover:bg-brand-50">Register</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if ($past->isNotEmpty() || request()->filled('search'))
        <div class="card mt-6">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <h2 class="font-bold text-slate-800">Past Events</h2>

                <form method="GET" class="flex items-center gap-2">
                    <div class="relative">
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" />
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search past events..." class="form-input w-56 pl-9 text-sm">
                    </div>
                    @if (request('search'))
                        <a href="{{ route('user.events.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">Reset</a>
                    @endif
                </form>
            </div>

            @if ($past->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-slate-400">No past events match your search.</div>
            @endif

            <div class="divide-y divide-slate-100">
                @foreach ($past as $event)
                    <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 opacity-70">
                        <div class="flex items-center gap-4">
                            <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-xl bg-slate-100 leading-none text-slate-500">
                                <span class="text-lg font-extrabold">{{ $event->starts_at->format('d') }}</span>
                                <span class="text-[10px] font-semibold uppercase">{{ $event->starts_at->format('M') }}</span>
                            </div>
                            <div>
                                <p class="font-bold text-slate-700">{{ $event->title }}</p>
                                @if ($event->subtitle)
                                    <p class="text-sm text-slate-400">{{ $event->subtitle }}</p>
                                @endif
                                <p class="mt-0.5 flex items-center gap-1 text-xs text-slate-400">
                                    <x-icon name="users" class="h-3.5 w-3.5" />
                                    {{ $event->registrations->count() }} {{ Str::plural('person', $event->registrations->count()) }} registered
                                </p>
                            </div>
                        </div>
                        <span class="badge badge-slate shrink-0">Ended</span>
                    </div>
                @endforeach
            </div>

            @if ($past->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $past->links() }}
                </div>
            @endif
        </div>
    @endif

</x-layout>
