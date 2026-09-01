@php
    $campaignRoute = $campaignRoute ?? null;
@endphp

<div class="card p-6">
    <h2 class="mb-4 font-bold text-slate-800">Lead Information</h2>
    <dl class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Company</dt>
            <dd class="mt-1.5 font-semibold text-slate-800">{{ $lead->company ?: '—' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Email</dt>
            <dd class="mt-1.5 font-semibold text-slate-800">{{ $lead->email ?: '—' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Status</dt>
            <dd class="mt-1.5"><span class="badge {{ $lead->statusBadgeClass() }}">{{ $lead->statusLabel() }}</span></dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Priority</dt>
            <dd class="mt-1.5"><span class="badge {{ $lead->priorityBadgeClass() }}">{{ ucfirst($lead->priority) }}</span></dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Assigned To</dt>
            <dd class="mt-1.5 font-semibold text-slate-800">{{ $lead->assignee?->name ?? 'Unassigned' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Source</dt>
            <dd class="mt-1.5 font-semibold text-slate-800">{{ $lead->source ?: '—' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Campaign</dt>
            <dd class="mt-1.5 font-semibold text-slate-800">
                @if ($lead->campaign)
                    @if ($campaignRoute)
                        <a href="{{ route($campaignRoute, $lead->campaign) }}" class="text-brand-700 hover:underline">{{ $lead->campaign->name }}</a>
                    @else
                        {{ $lead->campaign->name }}
                    @endif
                @else
                    —
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Interested Product</dt>
            <dd class="mt-1.5 font-semibold text-slate-800">{{ $lead->product ?: '—' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Next Follow-up</dt>
            <dd class="mt-1.5 font-semibold {{ $lead->isOverdue() ? 'text-red-600' : 'text-slate-800' }}">
                {{ $lead->next_follow_up_at?->format('d M Y') ?? '—' }}
                @if ($lead->isOverdue())
                    <span class="text-xs font-normal">(Overdue)</span>
                @endif
            </dd>
        </div>
        @if ($lead->status === 'won' && $lead->won_at)
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Won On</dt>
                <dd class="mt-1.5 font-semibold text-emerald-600">{{ $lead->won_at->format('d M Y') }}</dd>
            </div>
        @endif
        <div>
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Created By</dt>
            <dd class="mt-1.5 font-semibold text-slate-800">{{ $lead->creator?->name ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Created On</dt>
            <dd class="mt-1.5 font-semibold text-slate-800">{{ $lead->created_at->format('d M Y') }}</dd>
        </div>
    </dl>
</div>
