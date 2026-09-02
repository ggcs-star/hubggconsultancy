<x-layout title="My Team" title-icon="users" subtitle="Your downline, purchases and learning progress at a glance">

    @if ($apiError)
        <div class="card p-10 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-red-50">
                <x-icon name="x" class="h-7 w-7 text-red-500" />
            </div>
            <h3 class="mt-4 font-bold text-slate-800">Unable to load your team right now</h3>
            <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">We couldn't reach the team service. Please try again in a moment.</p>
        </div>
    @else
        @if ($truncated)
            <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                Your team is deeper than what's shown here — only the first few levels are displayed.
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card icon="users" color="brand" :value="$stats['total_members']" label="Total Members" />
            <x-stat-card icon="check-circle" color="success" :value="$stats['purchased_count']" label="Purchased" :description="$stats['purchased_percent'] . '%'" />
            <x-stat-card icon="video" color="chart-4" :value="$stats['videos_complete_count']" label="Videos Complete" :description="$stats['videos_complete_percent'] . '%'" />
            <x-stat-card icon="document" color="warning" :value="$stats['documents_complete_count']" label="Documents Complete" :description="$stats['documents_complete_percent'] . '%'" />
        </div>

        <div class="mt-6 card" x-data="{ tab: 'tree', search: '', level: '' }">
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
                    />
                @else
                    <p class="text-center text-sm text-slate-400">No team data available.</p>
                @endif
            </div>

            <div x-show="tab === 'members'" x-cloak>
                <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative w-full sm:max-w-xs">
                        <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input type="text" x-model="search" placeholder="Search team member..." class="form-input pl-10">
                    </div>
                    <select x-model="level" class="form-input w-full sm:w-40">
                        <option value="">All Levels</option>
                        @foreach ($rows->pluck('level')->unique()->sort()->values() as $lvl)
                            <option value="{{ $lvl }}">Level {{ $lvl }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs uppercase tracking-wide text-slate-400">
                                <th class="px-5 py-3 font-semibold">Member</th>
                                <th class="px-5 py-3 font-semibold">Level</th>
                                <th class="px-5 py-3 font-semibold">Joined</th>
                                <th class="px-5 py-3 font-semibold">Videos</th>
                                <th class="px-5 py-3 font-semibold">Documents</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-slate-800">{{ $user->name }} <span class="badge badge-slate ml-1 align-middle">YOU</span></p>
                                    @if ($rootPurchase)
                                        <p class="text-xs text-slate-400">{{ $rootPurchase['plan'] }} · #{{ $rootPurchase['purchase_code'] }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-500">—</td>
                                <td class="px-5 py-3.5 text-slate-500">—</td>
                                <td class="px-5 py-3.5 text-slate-500">{{ ! is_null($ownVideoPercent) ? $ownVideoPercent . '%' : '—' }}</td>
                                <td class="px-5 py-3.5 text-slate-500">{{ ! is_null($ownDocumentPercent) ? $ownDocumentPercent . '%' : '—' }}</td>
                            </tr>

                            @forelse ($rows as $row)
                                <tr x-show="(search === '' || '{{ Str::lower($row->name) }}'.includes(search.toLowerCase())) && (level === '' || level == {{ $row->level }})">
                                    <td class="px-5 py-3.5">
                                        <p class="font-semibold text-slate-800">{{ $row->name }}</p>
                                        @if ($row->purchase_code)
                                            <p class="text-xs text-slate-400">#{{ $row->purchase_code }}</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-500">Level {{ $row->level }}</td>
                                    <td class="px-5 py-3.5 text-slate-500">{{ $row->joined_at ? \Illuminate\Support\Carbon::parse($row->joined_at)->format('d M Y') : '—' }}</td>
                                    <td class="px-5 py-3.5 text-slate-500">
                                        @if ($row->on_platform)
                                            {{ $row->video_percent }}%
                                        @else
                                            <span title="Not on this platform yet">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-500">
                                        @if ($row->on_platform)
                                            {{ $row->document_percent }}%
                                        @else
                                            <span title="Not on this platform yet">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">You haven't built your team yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

</x-layout>
