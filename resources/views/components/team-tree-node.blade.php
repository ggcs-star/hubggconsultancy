@props([
    'node',
    'isRoot' => false,
    'color' => null,
    'progressByUserId' => [],
    'isFirst' => true,
    'isLast' => true,
    'totalMembers' => null,
    'rootPurchase' => null,
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

    $c = $isRoot ? null : ($color ?? $palette[0]);
    $children = $node['children'] ?? [];
    $hasChildren = count($children) > 0;

    $name = $node['user']['name'] ?? '—';
    $username = $node['user']['username'] ?? null;
    $userId = isset($node['user']['user_id']) ? (string) $node['user']['user_id'] : null;
    $progress = $userId ? ($progressByUserId[$userId] ?? null) : null;
    $onPlatform = $progress->on_platform ?? false;

    $initials = collect(preg_split('/\s+/', trim($name)))
        ->filter()
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

<div class="relative flex w-max flex-col items-center px-4 pt-6">
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
        @else
            <p class="mt-2.5 flex items-center gap-1.5 text-xs text-slate-400">
                <x-icon name="users" class="h-3.5 w-3.5" /> {{ count($children) }} {{ Str::plural('Member', count($children)) }}
            </p>

            <div class="mt-3 space-y-2">
                <div>
                    <div class="flex items-center justify-between text-[11px] text-slate-400">
                        <span>Videos</span>
                        <span class="font-semibold text-slate-600">{{ $onPlatform && ! is_null($progress->video_percent ?? null) ? $progress->video_percent . '%' : '—' }}</span>
                    </div>
                    <div class="mt-1 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full {{ $c['bar'] }}" style="width: {{ $onPlatform ? ($progress->video_percent ?? 0) : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between text-[11px] text-slate-400">
                        <span>Documents</span>
                        <span class="font-semibold text-slate-600">{{ $onPlatform && ! is_null($progress->document_percent ?? null) ? $progress->document_percent . '%' : '—' }}</span>
                    </div>
                    <div class="mt-1 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full {{ $c['bar'] }}" style="width: {{ $onPlatform ? ($progress->document_percent ?? 0) : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'team-member-{{ $userId }}')" class="mt-3.5 flex w-full items-center justify-center gap-1 rounded-lg border border-slate-200 py-2 text-xs font-semibold text-brand-700 transition hover:bg-slate-50">
                View Profile
                <x-icon name="chevron-right" class="h-3.5 w-3.5" />
            </button>
        @endif
    </div>

    @unless ($isRoot)
        <x-modal :name="'team-member-' . $userId" :show="false" max-width="md">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full text-base font-bold {{ $c['avatar'] }}">
                            {{ $initials !== '' ? $initials : '?' }}
                        </span>
                        <div>
                            <p class="font-bold text-slate-800">{{ $name }}</p>
                            @if ($username)
                                <p class="text-sm text-slate-400">{{ $username }}</p>
                            @endif
                        </div>
                    </div>
                    <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
                        <x-icon name="x" class="h-5 w-5" />
                    </button>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-slate-400">Level</p>
                        <p class="mt-0.5 font-semibold text-slate-800">Level {{ $node['level'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Status</p>
                        <p class="mt-0.5 font-semibold {{ ! empty($node['purchase_code']) ? 'text-emerald-600' : 'text-slate-400' }}">
                            {{ ! empty($node['purchase_code']) ? 'Purchased' : '—' }}
                        </p>
                    </div>
                    @if (! empty($node['purchase_code']))
                        <div>
                            <p class="text-xs text-slate-400">Purchase Code</p>
                            <p class="mt-0.5 font-semibold text-slate-800">#{{ $node['purchase_code'] }}</p>
                        </div>
                    @endif
                    @if (! empty($node['user']['joined_at']))
                        <div>
                            <p class="text-xs text-slate-400">Joined</p>
                            <p class="mt-0.5 font-semibold text-slate-800">{{ \Illuminate\Support\Carbon::parse($node['user']['joined_at'])->format('d M Y') }}</p>
                        </div>
                    @endif
                </div>

                <div class="mt-5 space-y-3 border-t border-slate-100 pt-4">
                    @if ($onPlatform)
                        <div>
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Videos</span>
                                <span class="font-semibold text-slate-600">{{ ! is_null($progress->video_percent ?? null) ? $progress->video_percent . '%' : '—' }}</span>
                            </div>
                            <div class="mt-1 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full {{ $c['bar'] }}" style="width: {{ $progress->video_percent ?? 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Documents</span>
                                <span class="font-semibold text-slate-600">{{ ! is_null($progress->document_percent ?? null) ? $progress->document_percent . '%' : '—' }}</span>
                            </div>
                            <div class="mt-1 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full {{ $c['bar'] }}" style="width: {{ $progress->document_percent ?? 0 }}%"></div>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-slate-400">This member hasn't joined this platform yet, so learning progress isn't available.</p>
                    @endif
                </div>
            </div>
        </x-modal>
    @endunless

    @if ($hasChildren)
        <div class="h-6 w-px {{ $isRoot ? 'bg-brand-300' : $c['line'] }}"></div>

        <div class="flex items-start">
            @foreach ($children as $child)
                <x-team-tree-node
                    :node="$child"
                    :color="$isRoot ? $palette[$loop->index % count($palette)] : $c"
                    :progress-by-user-id="$progressByUserId"
                    :is-first="$loop->first"
                    :is-last="$loop->last"
                />
            @endforeach
        </div>
    @endif
</div>
