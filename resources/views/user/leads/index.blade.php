<x-layout title="Leads / CRM" title-icon="users" subtitle="Manage and track every lead across your sales pipeline">

    @include('partials.lead-stat-cards')

    <div class="mt-6 card p-5" x-data="{ showFilters: true }">
        <div class="flex items-center justify-between">
            <p class="flex items-center gap-2 text-sm font-bold text-slate-800">
                <x-icon name="filter" class="h-4 w-4 text-brand-600" />
                Filters
            </p>
            <button type="button" x-on:click="showFilters = !showFilters" class="flex items-center gap-1 text-sm font-semibold text-brand-700 hover:text-brand-800">
                <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
                <x-icon name="chevron-down" class="h-4 w-4 transition" x-bind:class="showFilters ? 'rotate-180' : ''" />
            </button>
        </div>

        <form method="GET" x-show="showFilters" x-cloak class="mt-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-500">Search Leads</label>
                    <div class="relative">
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by lead name, contact..." class="form-input pl-9">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-500">Status</label>
                    <select name="status" class="form-input">
                        <option value="">All Statuses</option>
                        @foreach ($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-500">Campaign</label>
                    <select name="campaign_id" class="form-input">
                        <option value="">All Campaigns</option>
                        @foreach ($campaigns as $campaign)
                            <option value="{{ $campaign->id }}" @selected(request('campaign_id') == $campaign->id)>{{ $campaign->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-500">Product</label>
                    <input type="text" name="product" value="{{ request('product') }}" placeholder="Filter by product..." class="form-input">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-500">Value Range (₹)</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="value_min" min="0" step="0.01" value="{{ request('value_min') }}" placeholder="Min" class="form-input">
                        <span class="text-slate-400">–</span>
                        <input type="number" name="value_max" min="0" step="0.01" value="{{ request('value_max') }}" placeholder="Max" class="form-input">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-500">Follow-up Date</label>
                    <div class="flex items-center gap-2">
                        <input type="date" name="follow_up_from" value="{{ request('follow_up_from') }}" class="form-input">
                        <span class="text-slate-400">–</span>
                        <input type="date" name="follow_up_to" value="{{ request('follow_up_to') }}" class="form-input">
                    </div>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap items-center justify-end gap-3 border-t border-slate-100 pt-4">
                @if (request()->anyFilled(['search', 'status', 'campaign_id', 'product', 'value_min', 'value_max', 'follow_up_from', 'follow_up_to']))
                    <a href="{{ route('user.leads.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Clear All</a>
                @endif
                <button type="submit" class="btn-primary">
                    <x-icon name="filter" class="h-4 w-4" />
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
        <a href="{{ route('user.leads.create') }}" class="btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Add Lead
        </a>
    </div>

    <div class="mt-4 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">My Leads</h2>
            <p class="mt-0.5 text-xs text-slate-400">{{ $leads->total() }} total lead{{ $leads->total() === 1 ? '' : 's' }}</p>
        </div>

        @if ($leads->count())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-400">
                            <th class="px-5 py-3 font-semibold">Lead</th>
                            <th class="px-5 py-3 font-semibold">Contact</th>
                            <th class="px-5 py-3 font-semibold">Product / Value</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold">Follow-up</th>
                            <th class="px-5 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($leads as $lead)
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        @include('partials.lead-avatar', ['lead' => $lead])
                                        <div class="min-w-0">
                                            <a href="{{ route('user.leads.show', $lead) }}" class="font-semibold text-slate-800 hover:text-brand-700">{{ $lead->name }}</a>
                                            @if ($lead->company)
                                                <p class="text-xs text-slate-400">{{ $lead->company }}</p>
                                            @endif
                                            @if ($lead->campaign)
                                                <span class="badge badge-slate mt-1">{{ $lead->campaign->name }}</span>
                                            @endif
                                        </div>
                                    </div>
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
                                <td class="px-5 py-4">
                                    <select
                                        class="badge {{ $lead->statusBadgeClass() }} cursor-pointer border-0 py-1.5 pr-7 focus:outline-none focus:ring-2 focus:ring-brand-400"
                                        x-data
                                        x-on:change="
                                            fetch('{{ route('user.leads.status.update', $lead) }}', {
                                                method: 'POST',
                                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                                                body: new URLSearchParams({ _method: 'PATCH', status: $event.target.value }),
                                            }).then(() => window.location.reload());
                                        "
                                    >
                                        @foreach ($statusLabels as $value => $label)
                                            <option value="{{ $value }}" @selected($lead->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
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
                                        <a href="{{ route('user.leads.show', $lead) }}" title="View" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-500 transition hover:bg-slate-100">
                                            <x-icon name="eye" class="h-4 w-4" />
                                        </a>
                                        <a href="{{ route('user.leads.edit', $lead) }}" title="Edit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-violet-200 bg-violet-50 text-violet-600 transition hover:bg-violet-100">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                        </a>
                                        <form method="POST" action="{{ route('user.leads.destroy', $lead) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete lead \'{{ $lead->name }}\'? This cannot be undone.', target: $el })">
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

            <div class="border-t border-slate-100 px-5 py-4">
                {{ $leads->links() }}
            </div>
        @else
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                    <x-icon name="users" class="h-7 w-7 text-brand-600" />
                </div>
                <h3 class="mt-4 font-bold text-slate-800">No leads yet</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">
                    @if (request()->anyFilled(['search', 'status', 'campaign_id', 'product', 'value_min', 'value_max', 'follow_up_from', 'follow_up_to']))
                        Try changing your search or filters.
                    @else
                        Click "Add Lead" to create the first one.
                    @endif
                </p>
            </div>
        @endif
    </div>

</x-layout>
