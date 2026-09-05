<x-layout title="My Team" title-icon="users" subtitle="Your downline, purchases and learning progress at a glance">

    @if ($apiError)
        <div class="card p-10 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-red-50">
                <x-icon name="x" class="h-7 w-7 text-red-500" />
            </div>
            <h3 class="mt-4 font-bold text-slate-800">Unable to load your team right now</h3>
            <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">We're having trouble connecting to the GG Prime service right now. Please try again in a few minutes, or contact your administrator if the issue continues.</p>
        </div>
    @else
        @if ($truncated)
            <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                Your team is deeper than what's shown here — only the first few levels are displayed.
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-stat-card icon="users" color="brand" :value="$stats['total_members']" label="Total Members" />
            <x-stat-card icon="check-circle" color="success" :value="$stats['purchased_count']" label="Purchased" :description="$stats['purchased_percent'] . '%'" />
            <x-stat-card icon="check-circle" color="chart-4" :value="$stats['onboarding_complete_count']" label="Onboarding Complete" :description="$stats['onboarding_complete_percent'] . '%'" />
        </div>

        <div class="mt-6 card" x-data="{ tab: '{{ request()->hasAny(['search', 'level']) ? 'members' : 'tree' }}' }" x-on:team-view-all-members.window="tab = 'members'">
            <div class="flex items-center gap-1 border-b border-slate-100 px-5 pt-4">
                <button type="button" x-on:click="tab = 'tree'" class="rounded-t-lg px-3 py-2 text-sm font-semibold transition" :class="tab === 'tree' ? 'border-b-2 border-brand-600 text-brand-700' : 'text-slate-400 hover:text-slate-600'">
                    Team Tree
                </button>
                <button type="button" x-on:click="tab = 'members'" class="rounded-t-lg px-3 py-2 text-sm font-semibold transition" :class="tab === 'members' ? 'border-b-2 border-brand-600 text-brand-700' : 'text-slate-400 hover:text-slate-600'">
                    Members
                </button>
            </div>

            <div x-show="tab === 'tree'" x-cloak class="overflow-x-auto p-6">
                @if ($rootNode)
                    <x-team-tree-node
                        :node="$rootNode"
                        :is-root="true"
                        :progress-by-user-id="$progressByUserId"
                        :total-members="$stats['total_members']"
                        :root-purchase="$rootPurchase"
                        :own-checklist="$ownChecklist"
                        :own-kyc-verified="$ownKycVerified"
                    />
                @else
                    <p class="text-center text-sm text-slate-400">No team data available.</p>
                @endif
            </div>

            <div x-show="tab === 'members'" x-cloak>
                <form method="GET" class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative w-full sm:max-w-xs">
                        <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search team member..." class="form-input pl-10 {{ request('search') ? 'pr-9' : '' }}">
                        @if (request('search'))
                            <a href="{{ route('user.team.index', ['level' => request('level')]) }}" title="Clear search" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <x-icon name="x" class="h-4 w-4" />
                            </a>
                        @endif
                    </div>
                    <select name="level" class="form-input w-full sm:w-40" onchange="this.form.submit()">
                        <option value="">All Levels</option>
                        @foreach ($rows->pluck('level')->unique()->sort()->values() as $lvl)
                            <option value="{{ $lvl }}" @selected(request('level') == $lvl)>Level {{ $lvl }}</option>
                        @endforeach
                    </select>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs uppercase tracking-wide text-slate-400">
                                <th class="px-5 py-3 font-semibold">Member</th>
                                <th class="px-5 py-3 font-semibold">Level</th>
                                <th class="px-5 py-3 font-semibold">Joined</th>
                                <th class="px-5 py-3 font-semibold">Profile</th>
                                <th class="px-5 py-3 font-semibold">Onboarding</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-slate-800">{{ $rootNode['user']['name'] ?? $user->name }} <span class="badge badge-slate ml-1 align-middle">YOU</span></p>
                                    @if ($rootPurchase)
                                        <p class="text-xs text-slate-400">{{ $rootPurchase['plan'] }} · #{{ $rootPurchase['purchase_code'] }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-500">—</td>
                                <td class="px-5 py-3.5 text-slate-500">—</td>
                                <td class="px-5 py-3.5">
                                    @if (is_null($ownKycVerified))
                                        <span class="text-slate-400">—</span>
                                    @elseif ($ownKycVerified)
                                        <span class="badge badge-green">Verified</span>
                                    @else
                                        <span class="badge badge-slate">Incomplete</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <x-checklist-dots :checklist="$ownChecklist" :on-platform="true" />
                                </td>
                            </tr>

                            @forelse ($memberRows as $row)
                                <tr>
                                    <td class="px-5 py-3.5">
                                        <p class="font-semibold text-slate-800">{{ $row->name }}</p>
                                        @if ($row->purchase_code)
                                            <p class="text-xs text-slate-400">#{{ $row->purchase_code }}</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-500">Level {{ $row->level }}</td>
                                    <td class="px-5 py-3.5 text-slate-500">{{ $row->joined_at ? \Illuminate\Support\Carbon::parse($row->joined_at)->format('d M Y') : '—' }}</td>
                                    <td class="px-5 py-3.5">
                                        @if (is_null($row->kyc_verified))
                                            <span class="text-slate-400">—</span>
                                        @elseif ($row->kyc_verified)
                                            <span class="badge badge-green">Verified</span>
                                        @else
                                            <span class="badge badge-slate">Incomplete</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if ($row->on_platform)
                                            <x-checklist-dots :checklist="$row->checklist" :on-platform="true" />
                                        @else
                                            <span title="This member hasn't registered on this platform yet" class="badge badge-slate">Unregistered</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">
                                        @if (request()->hasAny(['search', 'level']))
                                            No members match your search or filter.
                                        @else
                                            You haven't built your team yet.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-4">
                    {{ $memberRows->links() }}
                </div>
            </div>
        </div>
    @endif

</x-layout>
