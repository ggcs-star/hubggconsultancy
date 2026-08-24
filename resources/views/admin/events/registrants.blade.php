<x-layout title="Event Registrants">

    <a href="{{ route('admin.events.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
        <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
        Back to Events
    </a>

    <div class="card mt-4 p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-2xl bg-brand-50 leading-none text-brand-700">
                    <span class="text-lg font-extrabold">{{ $event->starts_at->format('d') }}</span>
                    <span class="text-xs font-semibold uppercase">{{ $event->starts_at->format('M') }}</span>
                </div>
                <div>
                    <p class="font-bold text-slate-800">{{ $event->title }}</p>
                    @if ($event->subtitle)
                        <p class="text-sm text-slate-400">{{ $event->subtitle }}</p>
                    @endif
                    <p class="mt-1 text-sm text-slate-400">
                        {{ $event->starts_at->format('D, d M Y · h:i A') }}
                        @if ($event->ends_at)
                            &ndash; {{ $event->ends_at->format('h:i A') }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="text-right">
                <p class="text-2xl font-extrabold text-brand-700">{{ $registrants->count() }}</p>
                <p class="text-xs text-slate-400">{{ Str::plural('Registrant', $registrants->count()) }}</p>
            </div>
        </div>
    </div>

    <div class="mt-6 card">
        @if ($registrants->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                    <x-icon name="users" class="h-7 w-7 text-brand-600" />
                </div>
                <h3 class="mt-4 font-bold text-slate-800">No registrants yet</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">Once salespeople register for this event, they'll show up here.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($registrants as $registrant)
                    <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-700 text-xs font-semibold text-white">
                                {{ strtoupper(substr($registrant->name, 0, 1)) }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $registrant->name }}</p>
                                <p class="text-xs text-slate-400">{{ $registrant->email }}</p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-400">Registered {{ $registrant->pivot->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</x-layout>
