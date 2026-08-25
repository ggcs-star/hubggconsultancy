<x-layout title="Lead Detail">

    <a href="{{ route('user.leads.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
        <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
        Back to Leads
    </a>

    <div class="card mt-4 p-6">
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
                        <span class="badge badge-slate">{{ $lead->campaign->name }}</span>
                    @endif
                </div>
                @if ($lead->product || $lead->expected_value)
                    <p class="mt-2 text-sm text-slate-600">
                        {{ $lead->product ?: 'Interested product not set' }}
                        @if ($lead->expected_value)
                            &middot; <span class="font-semibold text-brand-700">₹{{ number_format($lead->expected_value, 0) }}</span>
                        @endif
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-6 card p-6">
        <h2 class="font-bold text-slate-800">Update Status & Follow-up</h2>
        <form method="POST" action="{{ route('user.leads.status.update', $lead) }}" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3 sm:items-end">
            @csrf
            @method('PATCH')

            <div>
                <label class="form-label">Status</label>
                <select name="status" required class="form-input">
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($lead->status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Next Follow-up Date</label>
                <input type="date" name="next_follow_up_at" value="{{ $lead->next_follow_up_at?->format('Y-m-d') }}" class="form-input">
            </div>

            <button type="submit" class="btn-primary">Save</button>
        </form>
    </div>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Activity & Notes</h2>
        </div>

        <form method="POST" action="{{ route('user.leads.notes.store', $lead) }}" class="space-y-3 border-b border-slate-100 p-5">
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
