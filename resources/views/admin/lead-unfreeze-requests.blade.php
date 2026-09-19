<x-layout title="Unfreeze Requests" subtitle="Review leads that froze from inactivity and approve or reject salesperson requests for more time">

    @php
        $statusMap = ['pending' => 'badge-amber', 'approved' => 'badge-green', 'rejected' => 'badge-slate'];
    @endphp

    <form method="GET" class="flex flex-wrap items-end gap-4 rounded-xl border border-slate-200 bg-white p-4">
        <div class="w-full sm:w-64">
            <label class="form-label">Search</label>
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ $search }}" placeholder="Lead, company, contact, salesperson..." class="form-input pl-10">
            </div>
        </div>

        <div class="w-full sm:w-48">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">All</option>
                <option value="pending" @selected($status === 'pending')>Pending</option>
                <option value="approved" @selected($status === 'approved')>Approved</option>
                <option value="rejected" @selected($status === 'rejected')>Rejected</option>
            </select>
        </div>

        <button type="submit" class="btn-primary">Apply Filters</button>

        @if ($search !== '' || $status !== 'pending')
            <a href="{{ route('admin.lead-unfreeze-requests') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Reset Filters</a>
        @endif
    </form>

    <div class="mt-4 card">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-400">
                        <th class="px-5 py-3 font-semibold">Lead</th>
                        <th class="px-5 py-3 font-semibold">Assigned To</th>
                        <th class="px-5 py-3 font-semibold">Request Message</th>
                        <th class="px-5 py-3 font-semibold">Requested On</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($requests as $lead)
                        <tr>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.leads.show', $lead) }}" class="font-medium text-slate-700 hover:text-brand-700">{{ $lead->name }}</a>
                                @if ($lead->company)
                                    <p class="text-xs text-slate-400">{{ $lead->company }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $lead->assignee?->name ?? '—' }}</td>
                            <td class="max-w-xs px-5 py-3.5 text-slate-500">{{ $lead->unfreeze_request_message ?: '—' }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $lead->unfreeze_requested_at?->format('d M Y, h:i A') ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="badge {{ $statusMap[$lead->unfreeze_status] ?? 'badge-slate' }}">{{ ucfirst($lead->unfreeze_status) }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($lead->unfreeze_status === 'pending')
                                        <form method="POST" action="{{ route('admin.lead-unfreeze-requests.approve', $lead) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-600 hover:bg-emerald-100">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.lead-unfreeze-requests.reject', $lead) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100">Reject</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400">
                                            {{ $lead->unfreezeReviewer?->name ?? 'Admin' }} &middot; {{ $lead->unfreeze_reviewed_at?->format('d M Y') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">No unfreeze requests match this filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($requests->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

</x-layout>
