@props(['checklist' => [], 'onPlatform' => true])

@if (count($checklist))
    <div class="flex flex-wrap items-center gap-1.5">
        @foreach ($checklist as $item)
            <span
                title="{{ $item->title }}"
                class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[10px] font-bold {{ $onPlatform && $item->completed ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400' }}"
            >
                {{ mb_strtoupper(mb_substr($item->title, 0, 1)) }}
            </span>
        @endforeach
    </div>
@else
    <span class="text-slate-400">—</span>
@endif
