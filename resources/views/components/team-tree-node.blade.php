@props([
    'node',
    'isRoot' => false,
    'colorIndex' => 0,
    'progressByUserId' => [],
    'isFirst' => true,
    'isLast' => true,
    'totalMembers' => null,
    'rootPurchase' => null,
    'ownChecklist' => [],
    'ownKycVerified' => null,
])

@php
    $palette = [
        ['avatar' => 'bg-violet-100 text-violet-700', 'line' => 'bg-violet-300', 'badge' => 'bg-violet-50 text-violet-700', 'bar' => 'bg-violet-500'],
        ['avatar' => 'bg-emerald-100 text-emerald-700', 'line' => 'bg-emerald-300', 'badge' => 'bg-emerald-50 text-emerald-700', 'bar' => 'bg-emerald-500'],
        ['avatar' => 'bg-amber-100 text-amber-700', 'line' => 'bg-amber-300', 'badge' => 'bg-amber-50 text-amber-700', 'bar' => 'bg-amber-500'],
        ['avatar' => 'bg-sky-100 text-sky-700', 'line' => 'bg-sky-300', 'badge' => 'bg-sky-50 text-sky-700', 'bar' => 'bg-sky-500'],
        ['avatar' => 'bg-rose-100 text-rose-700', 'line' => 'bg-rose-300', 'badge' => 'bg-rose-50 text-rose-700', 'bar' => 'bg-rose-500'],
        ['avatar' => 'bg-indigo-100 text-indigo-700', 'line' => 'bg-indigo-300', 'badge' => 'bg-indigo-50 text-indigo-700', 'bar' => 'bg-indigo-500'],
    ];

    $c = $isRoot ? null : $palette[$colorIndex % count($palette)];
    $children = $node['children'] ?? [];
    $hasChildren = count($children) > 0;

    $name = $node['user']['name'] ?? '—';
    $username = $node['user']['username'] ?? null;
    $userId = isset($node['user']['user_id']) ? (string) $node['user']['user_id'] : null;
    $progress = $userId ? ($progressByUserId[$userId] ?? null) : null;
    $onPlatform = $progress->on_platform ?? false;
    $kycVerified = $isRoot ? $ownKycVerified : ($progress->kyc_verified ?? null);

    $initials = collect(preg_split('/\s+/', trim($name)))
        ->filter()
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

<div
    class="relative flex w-max flex-col items-center px-4 pt-6"
    @unless ($isRoot) x-data="{ expanded: false, loading: false, loaded: false }" @endunless
>
    @unless ($isRoot)
        @if (! $isFirst)
            <div class="absolute left-0 right-1/2 top-0 h-px {{ $c['line'] }}"></div>
        @endif
        @if (! $isLast)
            <div class="absolute left-1/2 right-0 top-0 h-px {{ $c['line'] }}"></div>
        @endif
        <div class="absolute left-1/2 top-0 h-6 w-px -translate-x-1/2 {{ $c['line'] }}"></div>
    @endunless

    <div class="w-64 shrink-0 rounded-xl border {{ $isRoot ? 'border-brand-300 bg-brand-50' : 'border-slate-200 bg-white' }} p-4 shadow-sm">
        <div class="flex items-center gap-3">
            @if ($isRoot)
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white">
                    <x-icon name="user" class="h-5 w-5" />
                </span>
            @else
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-bold {{ $c['avatar'] }}">
                    {{ $initials !== '' ? $initials : '?' }}
                </span>
            @endif
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-slate-800">{{ $name }}</p>
                @if ($username)
                    <p class="truncate text-xs text-slate-400">{{ $username }}</p>
                @endif
            </div>
        </div>

        <div class="mt-2.5 flex flex-wrap items-center gap-1.5">
            @if ($isRoot)
                <span class="badge badge-green">Active</span>
            @else
                <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $c['badge'] }}">LEVEL {{ $node['level'] }}</span>
                @if (! empty($node['purchase_code']))
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600">
                        <x-icon name="check" class="h-3 w-3" /> Purchased
                    </span>
                @endif
            @endif

            @if (! is_null($kycVerified))
                @if ($kycVerified)
                    <span class="badge badge-green">Profile Verified</span>
                @else
                    <span class="badge badge-slate">Profile Incomplete</span>
                @endif
            @endif
        </div>

        @if ($isRoot)
            @if ($rootPurchase)
                <p class="mt-2 text-xs text-slate-500">{{ $rootPurchase['plan'] }} &middot; #{{ $rootPurchase['purchase_code'] }}</p>
            @endif
            @if (! is_null($totalMembers))
                <p class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700">
                    <x-icon name="users" class="h-3.5 w-3.5" /> {{ $totalMembers }} Team Members
                </p>
            @endif

            @if (count($ownChecklist))
                <div class="mt-3">
                    <p class="text-[11px] text-slate-500">Onboarding</p>
                    <div class="mt-1.5">
                        <x-checklist-dots :checklist="$ownChecklist" :on-platform="true" />
                    </div>
                </div>
            @endif
        @else
            <p class="mt-2.5 flex items-center gap-1.5 text-xs text-slate-400">
                <x-icon name="users" class="h-3.5 w-3.5" /> {{ count($children) }} {{ Str::plural('Member', count($children)) }}
            </p>

            <div class="mt-3">
                <p class="text-[11px] text-slate-400">Onboarding</p>
                <div class="mt-1.5">
                    @if (! $onPlatform)
                        <span class="badge badge-slate">Unregistered</span>
                    @elseif (! empty($progress->checklist) && count($progress->checklist))
                        <x-checklist-dots :checklist="$progress->checklist" :on-platform="true" />
                    @else
                        <span class="text-xs text-slate-400">—</span>
                    @endif
                </div>
            </div>

            @if ($hasChildren)
                <button
                    type="button"
                    x-on:click="
                        expanded = !expanded;
                        if (expanded && ! loaded) {
                            loading = true;
                            fetch('{{ route('user.team.node', $userId) }}?color={{ $colorIndex }}')
                                .then(r => { if (! r.ok) throw new Error('stale'); return r.text(); })
                                .then(html => {
                                    $refs.panel.innerHTML = html;
                                    window.Alpine.initTree($refs.panel);
                                    loaded = true;
                                    loading = false;
                                })
                                .catch(() => {
                                    $refs.panel.innerHTML = '<p class=\'text-xs text-slate-400\'>Couldn&rsquo;t load. Please refresh the page and try again.</p>';
                                    loaded = true;
                                    loading = false;
                                });
                        }
                    "
                    class="mt-3.5 flex w-full items-center justify-center gap-1 rounded-lg border border-slate-200 py-2 text-xs font-semibold text-brand-700 transition hover:bg-slate-50 disabled:opacity-60"
                    :disabled="loading"
                >
                    <span x-show="!loading" x-text="expanded ? 'Hide Members' : 'View {{ count($children) }} {{ Str::plural('Member', count($children)) }}'"></span>
                    <span x-show="loading" x-cloak>Loading…</span>
                    <x-icon x-show="!loading" name="chevron-right" class="h-3.5 w-3.5 transition-transform" x-bind:class="expanded ? 'rotate-90' : ''" />
                </button>
            @endif

            @if (! empty($node['user']['joined_at']))
                <p class="mt-3 text-[11px] text-slate-400">
                    Joined {{ \Illuminate\Support\Carbon::parse($node['user']['joined_at'])->format('d M Y') }}
                </p>
            @endif
        @endif
    </div>

    @if ($isRoot && $hasChildren)
        {{-- Root always shows level 1 immediately. --}}
        <div class="h-6 w-px bg-brand-300"></div>

        <div class="flex items-start">
            @foreach ($children as $child)
                <x-team-tree-node
                    :node="$child"
                    :color-index="$loop->index"
                    :progress-by-user-id="$progressByUserId"
                    :is-first="$loop->first"
                    :is-last="$loop->last"
                />
            @endforeach
        </div>
    @endif

    @if (! $isRoot && $hasChildren)
        {{-- Everything past level 1 (including level 1's own downline) is
             collapsed by default and only fetched/rendered — as more full
             cards, side by side, same as this row — once the button above
             is clicked. --}}
        <div x-show="expanded" x-cloak class="h-6 w-px {{ $c['line'] }}"></div>
        <div x-show="expanded" x-cloak x-ref="panel" class="flex items-start"></div>
    @endif
</div>
