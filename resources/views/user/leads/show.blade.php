<x-layout title="Lead Detail" title-icon="users" :subtitle="$lead->name">

    <a href="{{ route('user.leads.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
        <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
        Back to Leads
    </a>

    <div class="card mt-4 p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-lg font-bold text-violet-700">
                    {{ strtoupper(substr($lead->company ?: $lead->name, 0, 2)) }}
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6">
        @include('partials.lead-detail-info', ['lead' => $lead])
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

</x-layout>
