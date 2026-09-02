@props(['node', 'isRoot' => false])

<div class="flex flex-col items-center">
    <div class="w-56 shrink-0 rounded-xl border px-4 py-3 text-center shadow-sm {{ $isRoot ? 'border-brand-600 bg-brand-50' : 'border-slate-200 bg-white' }}">
        <p class="truncate text-sm font-bold text-slate-800">{{ $node['user']['name'] ?? '—' }}</p>
        @if (! empty($node['user']['username']))
            <p class="mt-0.5 truncate text-xs text-slate-400">{{ $node['user']['username'] }}</p>
        @endif

        @if ($isRoot)
            <span class="mt-1.5 inline-block rounded-full bg-brand-600 px-2 py-0.5 text-[10px] font-semibold text-white">YOU</span>
        @elseif (! empty($node['purchase_code']))
            <span class="mt-1.5 inline-block rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500">#{{ $node['purchase_code'] }}</span>
        @endif
    </div>

    @if (! empty($node['children']))
        <div class="h-4 w-px bg-slate-200"></div>
        <div class="flex flex-wrap items-start justify-center gap-6">
            @foreach ($node['children'] as $child)
                <div class="flex flex-col items-center">
                    <div class="h-4 w-px bg-slate-200"></div>
                    <x-team-tree-node :node="$child" />
                </div>
            @endforeach
        </div>
    @endif
</div>
