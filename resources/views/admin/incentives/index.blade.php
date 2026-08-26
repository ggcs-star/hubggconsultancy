@php
    $typeBadge = [
        'points' => 'badge-slate',
        'bonus' => 'badge-green',
        'cash' => 'badge-green',
        'gift' => 'badge-amber',
    ];
@endphp

<x-layout title="Incentives & Earnings" title-icon="coin" subtitle="Every reward and earning granted to salespersons">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="w-full sm:max-w-sm">
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="form-input pl-10">
            </div>
        </form>

        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-incentive')" class="btn-primary shrink-0">
            <x-icon name="plus" class="h-4 w-4" />
            Add Incentive
        </button>
    </div>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">All Entries</h2>
            <p class="mt-0.5 text-xs text-slate-400">{{ $entries->total() }} {{ Str::plural('entry', $entries->total()) }} &middot; ₹{{ number_format($totalAmount, 2) }} total</p>
        </div>

        @if ($entries->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                    <x-icon name="coin" class="h-7 w-7 text-brand-600" />
                </div>
                <h3 class="mt-4 font-bold text-slate-800">No incentives yet</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">Contest rewards will appear here automatically once a contest ends, or click "Add Incentive" to grant one manually.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-400">
                            <th class="px-5 py-3 font-semibold">Salesperson</th>
                            <th class="px-5 py-3 font-semibold">Title</th>
                            <th class="px-5 py-3 font-semibold">Amount</th>
                            <th class="px-5 py-3 font-semibold">Type</th>
                            <th class="px-5 py-3 font-semibold">Source</th>
                            <th class="px-5 py-3 font-semibold">Date</th>
                            <th class="px-5 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($entries as $entry)
                            <tr>
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-slate-800">{{ $entry->user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $entry->user->email }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">
                                    {{ $entry->title }}
                                    @if ($entry->note)
                                        <p class="text-xs text-slate-400">{{ $entry->note }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-brand-700">₹{{ number_format($entry->amount, 2) }}</td>
                                <td class="px-5 py-3.5"><span class="badge {{ $typeBadge[$entry->type] ?? 'badge-slate' }}">{{ $entry->typeLabel() }}</span></td>
                                <td class="px-5 py-3.5 text-slate-500">{{ $entry->source === 'contest' ? ($entry->contest->name ?? 'Contest') : 'Manual' }}</td>
                                <td class="px-5 py-3.5 text-slate-400">{{ $entry->awarded_at->format('d M Y') }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <form method="POST" action="{{ route('admin.incentives.destroy', $entry) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Remove this incentive entry?', target: $el })">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Delete" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($entries->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $entries->links() }}
                </div>
            @endif
        @endif
    </div>

    <x-modal name="add-incentive" :show="$errors->isNotEmpty()" max-width="lg">
        <form method="POST" action="{{ route('admin.incentives.store') }}" class="flex max-h-[85vh] flex-col">
            @csrf

            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-bold text-slate-800">Add Incentive</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <div class="space-y-5 overflow-y-auto px-6 py-6">
                <div>
                    <label class="form-label">Salesperson</label>
                    <select name="user_id" required class="form-input">
                        <option value="">Select a user</option>
                        @foreach ($users as $option)
                            <option value="{{ $option->id }}" @selected(old('user_id') == $option->id)>{{ $option->name }} ({{ $option->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. Diwali Bonus" class="form-input">
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="form-label">Amount (₹)</label>
                        <input type="number" name="amount" step="0.01" min="0" required value="{{ old('amount') }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Type</label>
                        <select name="type" required class="form-input">
                            @foreach (['points' => 'Points', 'bonus' => 'Bonus', 'cash' => 'Cash', 'gift' => 'Gift'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label">Date</label>
                    <input type="date" name="awarded_at" value="{{ old('awarded_at', now()->format('Y-m-d')) }}" required class="form-input">
                </div>

                <div>
                    <label class="form-label">Note</label>
                    <textarea name="note" rows="2" class="form-input" placeholder="Optional">{{ old('note') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
                <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="btn-primary">Add Incentive</button>
            </div>
        </form>
    </x-modal>

</x-layout>
