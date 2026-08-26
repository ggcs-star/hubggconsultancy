@php
    $statusBadge = [
        'draft' => ['label' => 'Draft', 'class' => 'badge-slate'],
        'active' => ['label' => 'Active', 'class' => 'badge-green'],
        'completed' => ['label' => 'Completed', 'class' => 'badge-slate'],
    ];
@endphp

<x-layout title="Contests" title-icon="gift" subtitle="Sales contests approved salespersons can join">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="flex w-full flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative w-full sm:max-w-sm">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search contests..." class="form-input pl-10">
            </div>

            <select name="status" class="form-input w-full sm:w-48" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="completed" @selected(request('status') === 'completed')>Completed</option>
            </select>

            @if (request('search') || request('status'))
                <a href="{{ route('admin.contests.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Reset</a>
            @endif
        </form>

        <a href="{{ route('admin.contests.create') }}" class="btn-primary shrink-0">
            <x-icon name="plus" class="h-4 w-4" />
            Add Contest
        </a>
    </div>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">All Contests</h2>
            <p class="mt-0.5 text-xs text-slate-400">{{ $contests->total() }} total contest{{ $contests->total() === 1 ? '' : 's' }}</p>
        </div>

        @if ($contests->count())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1050px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-400">
                            <th class="px-5 py-3 font-semibold">Contest</th>
                            <th class="px-5 py-3 font-semibold">Duration</th>
                            <th class="px-5 py-3 font-semibold">Target Type</th>
                            <th class="px-5 py-3 font-semibold">Target</th>
                            <th class="px-5 py-3 font-semibold">Reward</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold">Participants</th>
                            <th class="px-5 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($contests as $contest)
                            @php $status = $contest->displayStatus(); @endphp
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-5 py-4">
                                    <p class="max-w-[220px] truncate font-semibold text-slate-800">{{ $contest->name }}</p>
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    {{ $contest->starts_at->format('d M') }} &ndash; {{ $contest->ends_at->format('d M Y') }}
                                </td>

                                <td class="px-5 py-4">
                                    <span class="badge badge-slate">{{ $contest->targetType->name ?? '—' }}</span>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $contest->targetLabel() }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $contest->reward ?: '—' }}</td>

                                <td class="px-5 py-4">
                                    <span class="badge {{ $statusBadge[$status]['class'] }}">{{ $statusBadge[$status]['label'] }}</span>
                                </td>

                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.contests.participants', $contest) }}" class="inline-flex items-center gap-1.5 font-semibold text-brand-700 hover:underline">
                                        <x-icon name="users" class="h-4 w-4" />
                                        {{ $contest->registrations_count }} / {{ $eligibleCount }} Participants
                                    </a>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.contests.edit', $contest) }}" title="Edit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-violet-200 bg-violet-50 text-violet-600 transition hover:bg-violet-100">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                        </a>

                                        <form method="POST" action="{{ route('admin.contests.active.toggle', $contest) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_active" value="{{ $contest->is_active ? '0' : '1' }}">
                                            <button type="submit" title="{{ $contest->is_active ? 'Set to Draft' : 'Publish' }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-green-200 bg-green-50 text-green-600 transition hover:bg-green-100">
                                                <x-icon name="{{ $contest->is_active ? 'eye-off' : 'check-circle' }}" class="h-4 w-4" />
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.contests.destroy', $contest) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete \'{{ $contest->name }}\'? This also removes everyone\'s registrations and achievements for it.', target: $el })">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100">
                                                <x-icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($contests->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $contests->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                    <x-icon name="gift" class="h-7 w-7 text-brand-600" />
                </div>
                <h3 class="mt-4 font-bold text-slate-800">No contests found</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">
                    @if (request('search') || request('status'))
                        Try changing your search or filters.
                    @else
                        Click "Add Contest" to create the first one.
                    @endif
                </p>
            </div>
        @endif
    </div>

</x-layout>
