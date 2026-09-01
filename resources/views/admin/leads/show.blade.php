<x-layout title="Lead Detail" title-icon="users" :subtitle="$lead->name">

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
        @include('partials.lead-detail-info', ['lead' => $lead, 'campaignRoute' => 'admin.campaigns.show'])
    </div>

</x-layout>
