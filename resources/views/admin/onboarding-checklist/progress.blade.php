<x-layout title="Onboarding Progress" title-icon="users" subtitle="Who has completed which checklist step">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="w-full sm:max-w-sm">
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name..." class="form-input pl-10">
            </div>
        </form>

        <a href="{{ route('admin.onboarding-checklist.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
            <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
            Back to Checklist
        </a>
    </div>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Salesperson Progress</h2>
            <p class="mt-0.5 text-xs text-slate-400">{{ $rows->count() }} salesperson{{ $rows->count() === 1 ? '' : 's' }} &middot; {{ $items->count() }} published step{{ $items->count() === 1 ? '' : 's' }}</p>
        </div>

        @if ($items->isEmpty())
            <div class="px-6 py-16 text-center text-sm text-slate-400">
                No published checklist steps yet — add some from the Checklist page first.
            </div>
        @elseif ($rows->isEmpty())
            <div class="px-6 py-16 text-center text-sm text-slate-400">
                No salespersons match your search.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-400">
                            <th class="sticky left-0 z-10 bg-slate-50 px-5 py-3 font-semibold">Salesperson</th>
                            @foreach ($items as $item)
                                <th class="px-3 py-3 text-center font-semibold" title="{{ $item->title }}">
                                    <span class="block max-w-[110px] truncate">{{ $item->title }}</span>
                                </th>
                            @endforeach
                            <th class="px-5 py-3 text-right font-semibold">Progress</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($rows as $row)
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="sticky left-0 z-10 bg-white px-5 py-3">
                                    <p class="font-semibold text-slate-800">{{ $row->user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $row->user->email }}</p>
                                </td>

                                @foreach ($items as $item)
                                    <td class="px-3 py-3 text-center">
                                        @if (in_array($item->id, $row->completed_ids))
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                                <x-icon name="check" class="h-3.5 w-3.5" />
                                            </span>
                                        @else
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-slate-300">
                                                <x-icon name="x" class="h-3.5 w-3.5" />
                                            </span>
                                        @endif
                                    </td>
                                @endforeach

                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <div class="h-2 w-20 overflow-hidden rounded-full bg-slate-100">
                                            <div class="h-full rounded-full bg-brand-600" style="width: {{ $row->percent }}%"></div>
                                        </div>
                                        <span class="w-16 shrink-0 text-right text-xs font-semibold text-slate-600">{{ $row->completed_count }}/{{ $items->count() }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</x-layout>
