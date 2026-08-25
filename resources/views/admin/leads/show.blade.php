<x-layout title="Lead Detail">

    <a href="{{ route('admin.leads.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
        <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
        Back to Leads
    </a>

    @if ($duplicates->isNotEmpty())
        <div class="card mt-4 flex items-start gap-3 border-l-4 border-l-amber-400 p-4">
            <x-icon name="help-circle" class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" />
            <div>
                <p class="text-sm font-semibold text-slate-700">Possible duplicate lead{{ $duplicates->count() > 1 ? 's' : '' }}</p>
                <p class="mt-0.5 text-sm text-slate-500">
                    This phone number also appears on:
                    @foreach ($duplicates as $duplicate)
                        <a href="{{ route('admin.leads.show', $duplicate) }}" class="font-semibold text-brand-700 hover:underline">{{ $duplicate->name }}</a>@if (! $loop->last), @endif
                    @endforeach
                </p>
            </div>
        </div>
    @endif

    <div class="card mt-4 p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-brand-700 text-lg font-bold text-white">
                    {{ strtoupper(substr($lead->name, 0, 1)) }}
                </span>
                <div>
                    <p class="text-lg font-bold text-slate-800">{{ $lead->name }}</p>
                    @if ($lead->company)
                        <p class="text-sm text-slate-400">{{ $lead->company }}</p>
                    @endif
                    <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-slate-500">
                        @if ($lead->email)
                            <span class="flex items-center gap-1"><x-icon name="mail" class="h-3.5 w-3.5" /> {{ $lead->email }}</span>
                        @endif
                        @if ($lead->phone)
                            <span class="flex items-center gap-1"><x-icon name="phone" class="h-3.5 w-3.5" /> {{ $lead->phone }}</span>
                        @endif
                        @if ($lead->source)
                            <span class="badge badge-slate">{{ $lead->source }}</span>
                        @endif
                        @if ($lead->campaign)
                            <a href="{{ route('admin.campaigns.show', $lead->campaign) }}" class="badge badge-slate hover:bg-slate-200">{{ $lead->campaign->name }}</a>
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.leads.edit', $lead) }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Edit Lead</a>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card p-5">
            <p class="text-xs text-slate-400">Status</p>
            <span class="badge {{ $lead->statusBadgeClass() }} mt-2">{{ $lead->statusLabel() }}</span>
        </div>
        <div class="card p-5">
            <p class="text-xs text-slate-400">Assigned To</p>
            <p class="mt-1 font-semibold text-slate-800">{{ $lead->assignee?->name ?? 'Unassigned' }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs text-slate-400">Product / Value</p>
            <p class="mt-1 font-semibold text-slate-800">
                {{ $lead->product ?: '—' }}
                @if ($lead->expected_value)
                    <span class="text-brand-700">(₹{{ number_format($lead->expected_value, 0) }})</span>
                @endif
            </p>
        </div>
        <div class="card p-5">
            <p class="text-xs text-slate-400">Next Follow-up</p>
            <p class="mt-1 font-semibold {{ $lead->isOverdue() ? 'text-red-600' : 'text-slate-800' }}">
                {{ $lead->next_follow_up_at?->format('d M Y') ?? '—' }}
                @if ($lead->isOverdue())
                    <span class="text-xs font-normal">(Overdue)</span>
                @endif
            </p>
        </div>
    </div>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Activity & Notes</h2>
        </div>

        <form method="POST" action="{{ route('admin.leads.notes.store', $lead) }}" class="space-y-3 border-b border-slate-100 p-5">
            @csrf
            <textarea name="note" rows="2" required placeholder="Add a note — e.g. called on 25 Aug, said call back next week" class="form-input"></textarea>
            <button type="submit" class="btn-primary">Add Note</button>
        </form>

        @if ($lead->notes->isEmpty())
            <div class="px-6 py-10 text-center text-sm text-slate-400">No notes yet.</div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($lead->notes as $note)
                    <div class="px-5 py-4">
                        <p class="text-sm text-slate-700">{{ $note->note }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ $note->user?->name ?? 'Unknown' }} &middot; {{ $note->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</x-layout>
