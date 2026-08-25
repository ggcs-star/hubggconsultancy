<x-layout title="My Team" title-icon="users" subtitle="Grow your team and track what you've earned from them">

    <div class="card p-6" x-data="{ copied: false }">
        <p class="font-bold text-slate-800">Your Referral Link</p>
        <p class="mt-1 text-sm text-slate-500">Share this link — anyone who signs up through it joins your team.</p>

        <div class="mt-4 flex flex-col gap-3 sm:flex-row">
            <input type="text" readonly value="{{ $referralUrl }}" class="form-input flex-1 truncate bg-slate-50 text-slate-600" x-ref="referralInput" onclick="this.select()">
            <button
                type="button"
                class="btn-primary shrink-0"
                x-on:click="
                    navigator.clipboard.writeText($refs.referralInput.value);
                    copied = true;
                    setTimeout(() => copied = false, 2000);
                "
            >
                <x-icon name="share" class="h-4 w-4" />
                <span x-text="copied ? 'Copied!' : 'Copy Link'"></span>
            </button>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="card p-6">
            <p class="text-2xl font-extrabold text-brand-700">{{ $user->teamMembers->count() }}</p>
            <p class="mt-1 text-sm text-slate-400">Team Members</p>
        </div>
        <div class="card p-6">
            <p class="text-2xl font-extrabold text-emerald-600">₹{{ number_format($totalEarnings, 2) }}</p>
            <p class="mt-1 text-sm text-slate-400">Total Earnings</p>
        </div>
        <div class="card p-6">
            <p class="text-2xl font-extrabold text-slate-800">{{ $user->referral_code }}</p>
            <p class="mt-1 text-sm text-slate-400">Your Referral Code</p>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="card">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="font-bold text-slate-800">Team Members</h2>
                <span class="badge badge-slate">{{ $user->teamMembers->count() }}</span>
            </div>

            @if ($user->teamMembers->isEmpty())
                <div class="px-6 py-10 text-center text-sm text-slate-400">No team members yet — share your referral link to start building your team.</div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($user->teamMembers as $member)
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
                <h2 class="font-bold text-slate-800">Earnings History</h2>
            </div>

            @if ($earnings->isEmpty())
                <div class="px-6 py-10 text-center text-sm text-slate-400">No earnings yet.</div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($earnings as $earning)
                        <div class="px-5 py-3.5">
                            <p class="text-sm font-semibold text-slate-800">
                                ₹{{ number_format($earning->amount, 2) }}
                                <span class="font-normal text-slate-400">— {{ $earning->referredUser->name }}</span>
                            </p>
                            @if ($earning->note)
                                <p class="mt-0.5 text-xs text-slate-400">{{ $earning->note }}</p>
                            @endif
                            <p class="mt-0.5 text-xs text-slate-400">{{ $earning->created_at->format('d M Y') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</x-layout>
