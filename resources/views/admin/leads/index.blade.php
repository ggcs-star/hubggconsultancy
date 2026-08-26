<x-layout title="Leads / CRM" title-icon="users" subtitle="Every lead across your sales team">

    <div class="grid grid-cols-5 gap-2 sm:gap-4">
        <div class="card p-3 sm:p-4">
            <p class="text-lg sm:text-2xl font-extrabold text-slate-800">{{ $stats['new'] }}</p>
            <p class="mt-0.5 text-xs text-slate-400">New Leads</p>
        </div>
        <div class="card p-3 sm:p-4">
            <p class="text-lg sm:text-2xl font-extrabold text-amber-600">{{ $stats['follow_ups'] }}</p>
            <p class="mt-0.5 text-xs text-slate-400">Follow-ups Due</p>
        </div>
        <div class="card p-3 sm:p-4">
            <p class="text-lg sm:text-2xl font-extrabold text-brand-700">{{ $stats['qualified'] }}</p>
            <p class="mt-0.5 text-xs text-slate-400">Qualified</p>
        </div>
        <div class="card p-3 sm:p-4">
            <p class="text-lg sm:text-2xl font-extrabold text-brand-700">{{ $stats['opportunities'] }}</p>
            <p class="mt-0.5 text-xs text-slate-400">Opportunities</p>
        </div>
        <div class="card p-3 sm:p-4">
            <p class="text-lg sm:text-2xl font-extrabold text-emerald-600">{{ $stats['won'] }}</p>
            <p class="mt-0.5 text-xs text-slate-400">Won</p>
        </div>
    </div>

    <div class="mt-4 card overflow-x-auto p-5">
        <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Pipeline (current stage distribution)</p>
        <div class="flex min-w-[700px] items-center gap-2">
            @foreach ($funnel as $status => $count)
                <div class="flex flex-1 flex-col items-center">
                    <div class="flex h-14 w-full items-center justify-center rounded-lg bg-brand-50 text-lg font-bold text-brand-700">{{ $count }}</div>
                    <p class="mt-1.5 text-center text-xs text-slate-500">{{ $statusLabels[$status] }}</p>
                </div>
                @if (! $loop->last)
                    <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-slate-300" />
                @endif
            @endforeach
        </div>
    </div>

    <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="flex w-full flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative w-full sm:max-w-xs">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search leads..." class="form-input pl-10">
            </div>

            <select name="status" class="form-input w-full sm:w-40" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach ($statusLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="assigned_to" class="form-input w-full sm:w-44" onchange="this.form.submit()">
                <option value="">All Salespersons</option>
                @foreach ($salespersons as $salesperson)
                    <option value="{{ $salesperson->id }}" @selected(request('assigned_to') == $salesperson->id)>{{ $salesperson->name }}</option>
                @endforeach
            </select>

            <select name="campaign_id" class="form-input w-full sm:w-44" onchange="this.form.submit()">
                <option value="">All Campaigns</option>
                @foreach ($campaigns as $campaign)
                    <option value="{{ $campaign->id }}" @selected(request('campaign_id') == $campaign->id)>{{ $campaign->name }}</option>
                @endforeach
            </select>

            @if (request()->anyFilled(['search', 'status', 'assigned_to', 'campaign_id']))
                <a href="{{ route('admin.leads.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Reset</a>
            @endif
        </form>

        <div class="flex shrink-0 items-center gap-2">
            <form method="POST" action="{{ route('admin.leads.auto-assign') }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Distribute every unassigned lead round-robin across approved salespersons?', target: $el })">
                @csrf
                <button type="submit" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Auto-Assign Unassigned</button>
            </form>
            <a href="{{ route('admin.campaigns.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Campaigns</a>
            <a href="{{ route('admin.leads.create') }}" class="btn-primary">
                <x-icon name="plus" class="h-4 w-4" />
                Add Lead
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.leads.bulk-assign') }}" x-data="{ selected: [] }">
        @csrf

        <div class="mt-4 card">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="font-bold text-slate-800">All Leads</h2>
                    <p class="mt-0.5 text-xs text-slate-400">{{ $leads->total() }} total lead{{ $leads->total() === 1 ? '' : 's' }}</p>
                </div>
                <div class="flex items-center gap-2" x-show="selected.length > 0" x-cloak>
                    <select name="assigned_to" required class="form-input w-48">
                        <option value="">Assign selected to...</option>
                        @foreach ($salespersons as $salesperson)
                            <option value="{{ $salesperson->id }}">{{ $salesperson->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary text-sm">
                        Assign <span x-text="selected.length"></span> lead(s)
                    </button>
                </div>
            </div>

            @if ($leads->count())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-400">
                                <th class="w-10 px-5 py-3"></th>
                                <th class="px-5 py-3 font-semibold">Lead</th>
                                <th class="px-5 py-3 font-semibold">Contact</th>
                                <th class="px-5 py-3 font-semibold">Product / Value</th>
                                <th class="px-5 py-3 font-semibold">Assigned To</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 font-semibold">Follow-up</th>
                                <th class="px-5 py-3 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($leads as $lead)
                                <tr class="transition hover:bg-slate-50/60">
                                    <td class="px-5 py-4">
                                        <input
                                            type="checkbox"
                                            value="{{ $lead->id }}"
                                            name="lead_ids[]"
                                            class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                                            x-on:change="$event.target.checked ? selected.push('{{ $lead->id }}') : selected = selected.filter(id => id !== '{{ $lead->id }}')"
                                        >
                                    </td>
                                    <td class="px-5 py-4">
                                        <a href="{{ route('admin.leads.show', $lead) }}" class="font-semibold text-slate-800 hover:text-brand-700">{{ $lead->name }}</a>
                                        @if ($lead->company)
                                            <p class="text-xs text-slate-400">{{ $lead->company }}</p>
                                        @endif
                                        @if ($lead->campaign)
                                            <span class="badge badge-slate mt-1">{{ $lead->campaign->name }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-slate-500">
                                        {{ $lead->phone ?: ($lead->email ?: '—') }}
                                    </td>
                                    <td class="px-5 py-4 text-slate-500">
                                        {{ $lead->product ?: '—' }}
                                        @if ($lead->expected_value)
                                            <p class="text-xs text-slate-400">₹{{ number_format($lead->expected_value, 0) }}</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ $lead->assignee?->name ?? '—' }}</td>
                                    <td class="px-5 py-4"><span class="badge {{ $lead->statusBadgeClass() }}">{{ $lead->statusLabel() }}</span></td>
                                    <td class="px-5 py-4">
                                        @if ($lead->next_follow_up_at)
                                            <span class="{{ $lead->isOverdue() ? 'font-semibold text-red-600' : 'text-slate-500' }}">
                                                {{ $lead->next_follow_up_at->format('d M Y') }}
                                                @if ($lead->isOverdue())
                                                    (Overdue)
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.leads.edit', $lead) }}" title="Edit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-violet-200 bg-violet-50 text-violet-600 transition hover:bg-violet-100">
                                                <x-icon name="pencil" class="h-4 w-4" />
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $leads->links() }}
                </div>
            @else
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                        <x-icon name="users" class="h-7 w-7 text-brand-600" />
                    </div>
                    <h3 class="mt-4 font-bold text-slate-800">No leads found</h3>
                    <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">
                        @if (request()->anyFilled(['search', 'status', 'assigned_to', 'campaign_id']))
                            Try changing your search or filters.
                        @else
                            Click "Add Lead" to create the first one.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </form>

</x-layout>
