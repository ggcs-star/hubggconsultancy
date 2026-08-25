<x-layout title="Team Detail">

    <a href="{{ route('admin.teams.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
        <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
        Back to Teams
    </a>

    <div class="card mt-4 p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-brand-700 text-lg font-bold text-white">
                    {{ strtoupper(substr($referrer->name, 0, 1)) }}
                </span>
                <div>
                    <p class="font-bold text-slate-800">{{ $referrer->name }}</p>
                    <p class="text-sm text-slate-400">{{ $referrer->email }}</p>
                    <p class="mt-1 text-sm text-slate-400">{{ $referrer->teamMembers->count() }} {{ Str::plural('team member', $referrer->teamMembers->count()) }}</p>
                </div>
            </div>

            <div class="text-right">
                <p class="text-2xl font-extrabold text-brand-700">₹{{ number_format($earnings->sum('amount'), 2) }}</p>
                <p class="text-xs text-slate-400">Total Earnings</p>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="card">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="font-bold text-slate-800">Team Members</h2>
                <span class="badge badge-slate">{{ $referrer->teamMembers->count() }}</span>
            </div>

            @if ($referrer->teamMembers->isEmpty())
                <div class="px-6 py-10 text-center text-sm text-slate-400">No team members yet.</div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($referrer->teamMembers as $member)
                        <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-500">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $member->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $member->email }}</p>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400">Joined {{ $member->created_at->format('d M Y') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="font-bold text-slate-800">Log an Earning</h2>
            </div>

            @if ($referrer->teamMembers->isEmpty())
                <div class="px-6 py-10 text-center text-sm text-slate-400">Add a team member first before logging earnings.</div>
            @else
                <form method="POST" action="{{ route('admin.teams.earnings.store', $referrer) }}" class="space-y-4 p-5">
                    @csrf

                    <div>
                        <label class="form-label">Team Member</label>
                        <select name="referred_user_id" required class="form-input">
                            <option value="">Select a team member</option>
                            @foreach ($referrer->teamMembers as $member)
                                <option value="{{ $member->id }}" @selected(old('referred_user_id') == $member->id)>{{ $member->name }} ({{ $member->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Amount (₹)</label>
                        <input type="number" name="amount" step="0.01" min="0.01" required placeholder="2000.00" value="{{ old('amount') }}" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Note</label>
                        <textarea name="note" rows="2" class="form-input" placeholder="e.g. Commission for January sale">{{ old('note') }}</textarea>
                    </div>

                    <button type="submit" class="btn-primary w-full">Log Earning</button>
                </form>
            @endif
        </div>
    </div>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Earnings History</h2>
        </div>

        @if ($earnings->isEmpty())
            <div class="px-6 py-10 text-center text-sm text-slate-400">No earnings logged yet.</div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($earnings as $earning)
                    <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800">
                                ₹{{ number_format($earning->amount, 2) }}
                                <span class="font-normal text-slate-400">— for {{ $earning->referredUser->name }}</span>
                            </p>
                            @if ($earning->note)
                                <p class="mt-0.5 truncate text-xs text-slate-400">{{ $earning->note }}</p>
                            @endif
                            <p class="mt-0.5 text-xs text-slate-400">
                                Logged {{ $earning->created_at->format('d M Y, h:i A') }}
                                @if ($earning->creator)
                                    by {{ $earning->creator->name }}
                                @endif
                            </p>
                        </div>
                        <form method="POST" action="{{ route('admin.referral-earnings.destroy', $earning) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Remove this ₹{{ number_format($earning->amount, 2) }} entry?', target: $el })">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</x-layout>
