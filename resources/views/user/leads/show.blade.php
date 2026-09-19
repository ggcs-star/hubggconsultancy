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

    @if ($lead->isFrozen())
        <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-5 py-4">
            <div class="flex items-start gap-3">
                <x-icon name="lock" class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" />
                <div class="flex-1">
                    <p class="font-semibold text-amber-800">This lead is frozen</p>
                    <p class="mt-1 text-sm text-amber-700">
                        Its status hasn't changed in {{ config('leads.freeze_after_days') }}+ day(s), so it's read-only until an admin approves an unfreeze request.
                    </p>

                    @if ($lead->hasPendingUnfreezeRequest())
                        <div class="mt-3 rounded-lg border border-amber-300 bg-white px-3 py-2 text-sm text-amber-700">
                            Your request is pending admin review: "{{ $lead->unfreeze_request_message }}"
                        </div>
                    @else
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'lead-unfreeze-{{ $lead->id }}')" class="btn-primary mt-3 text-sm">
                            Request Unfreeze
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <x-modal name="lead-unfreeze-{{ $lead->id }}" :show="false" max-width="md">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-800">Request Unfreeze</h2>
                    <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
                        <x-icon name="x" class="h-5 w-5" />
                    </button>
                </div>
                <p class="mt-1 text-sm text-slate-400">{{ $lead->name }}</p>

                <form method="POST" action="{{ route('user.leads.request-unfreeze', $lead) }}" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="form-label">Message to admin</label>
                        <textarea name="message" rows="3" required maxlength="1000" placeholder="Explain why you need this lead unfrozen..." class="form-input"></textarea>
                    </div>
                    <button type="submit" class="btn-primary w-full">Send Request</button>
                </form>
            </div>
        </x-modal>
    @endif

    <div class="mt-6">
        @include('partials.lead-detail-info', ['lead' => $lead])
    </div>

    <div class="mt-6 card p-6">
        <h2 class="font-bold text-slate-800">Update Status & Follow-up</h2>
        @if ($lead->isFrozen())
            <p class="mt-4 text-sm text-slate-400">Updating this lead is disabled while it's frozen.</p>
        @else
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
        @endif
    </div>

</x-layout>
