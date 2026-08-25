<x-layout title="Leads / CRM" title-icon="users" subtitle="Leads assigned to you">

    <div class="grid grid-cols-5 gap-2 sm:gap-4">
        <div class="card p-3 sm:p-4">
            <p class="text-lg sm:text-2xl font-extrabold text-slate-800">{{ $stats['new'] }}</p>
            <p class="mt-0.5 text-xs text-slate-400">New</p>
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

    <form method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative w-full sm:max-w-sm">
            <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone, company..." class="form-input pl-10">
        </div>

        <select name="status" class="form-input w-full sm:w-48" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            @foreach ($statusLabels as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>

        @if (request()->anyFilled(['search', 'status']))
            <a href="{{ route('user.leads.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Reset</a>
        @endif
    </form>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">My Leads</h2>
            <p class="mt-0.5 text-xs text-slate-400">{{ $leads->count() }} total lead{{ $leads->count() === 1 ? '' : 's' }}</p>
        </div>

        @if ($leads->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                    <x-icon name="users" class="h-7 w-7 text-brand-600" />
                </div>
                <h3 class="mt-4 font-bold text-slate-800">No leads yet</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">Leads your admin assigns to you will show up here.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($leads as $lead)
                    <a href="{{ route('user.leads.show', $lead) }}" class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 transition hover:bg-slate-50/60">
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-800">{{ $lead->name }}</p>
                            <p class="mt-0.5 text-xs text-slate-400">
                                {{ $lead->product ?: ($lead->company ?: ($lead->phone ?: $lead->email ?: 'No contact info')) }}
                                @if ($lead->expected_value)
                                    &middot; ₹{{ number_format($lead->expected_value, 0) }}
                                @endif
                            </p>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            @if ($lead->next_follow_up_at)
                                <span class="text-xs {{ $lead->isOverdue() ? 'font-semibold text-red-600' : 'text-slate-400' }}">
                                    Follow up {{ $lead->next_follow_up_at->format('d M Y') }}
                                    @if ($lead->isOverdue()) (Overdue) @endif
                                </span>
                            @endif
                            <span class="badge {{ $lead->statusBadgeClass() }}">{{ $lead->statusLabel() }}</span>
                            <x-icon name="chevron-right" class="h-4 w-4 text-slate-300" />
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

</x-layout>
