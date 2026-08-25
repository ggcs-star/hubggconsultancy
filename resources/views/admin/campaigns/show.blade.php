<x-layout title="Campaign Performance" :subtitle="$campaign->name">

    <a href="{{ route('admin.campaigns.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
        <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
        Back to Campaigns
    </a>

    <div class="mt-4 grid grid-cols-5 gap-2 sm:gap-4">
        <div class="card p-3 sm:p-4">
            <p class="text-lg sm:text-2xl font-extrabold text-slate-800">{{ $metrics->total }}</p>
            <p class="mt-0.5 text-xs text-slate-400">Total Leads</p>
        </div>
        <div class="card p-3 sm:p-4">
            <p class="text-lg sm:text-2xl font-extrabold text-slate-800">{{ $metrics->contacted }}</p>
            <p class="mt-0.5 text-xs text-slate-400">Contacted</p>
        </div>
        <div class="card p-3 sm:p-4">
            <p class="text-lg sm:text-2xl font-extrabold text-brand-700">{{ $metrics->qualified }}</p>
            <p class="mt-0.5 text-xs text-slate-400">Qualified+</p>
        </div>
        <div class="card p-3 sm:p-4">
            <p class="text-lg sm:text-2xl font-extrabold text-emerald-600">{{ $metrics->won }}</p>
            <p class="mt-0.5 text-xs text-slate-400">Converted</p>
        </div>
        <div class="card p-3 sm:p-4">
            <p class="text-lg sm:text-2xl font-extrabold text-emerald-600">₹{{ number_format($metrics->revenue, 0) }}</p>
            <p class="mt-0.5 text-xs text-slate-400">Revenue</p>
        </div>
    </div>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Leads in This Campaign</h2>
        </div>

        @if ($campaign->leads->isEmpty())
            <div class="px-6 py-16 text-center text-sm text-slate-400">No leads tagged to this campaign yet.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-400">
                            <th class="px-5 py-3 font-semibold">Lead</th>
                            <th class="px-5 py-3 font-semibold">Assigned To</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold">Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($campaign->leads as $lead)
                            <tr>
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('admin.leads.show', $lead) }}" class="font-medium text-slate-800 hover:text-brand-700">{{ $lead->name }}</a>
                                </td>
                                <td class="px-5 py-3.5 text-slate-500">{{ $lead->assignee?->name ?? '—' }}</td>
                                <td class="px-5 py-3.5"><span class="badge {{ $lead->statusBadgeClass() }}">{{ $lead->statusLabel() }}</span></td>
                                <td class="px-5 py-3.5 text-slate-600">{{ $lead->expected_value ? '₹' . number_format($lead->expected_value, 0) : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</x-layout>
