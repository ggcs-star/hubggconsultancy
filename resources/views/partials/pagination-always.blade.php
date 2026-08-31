<div class="flex flex-col-reverse items-center justify-between gap-3 sm:flex-row">
    <p class="text-sm text-slate-500">
        @if ($paginator->total() > 0)
            Showing <span class="font-medium text-slate-700">{{ $paginator->firstItem() }}</span> to <span class="font-medium text-slate-700">{{ $paginator->lastItem() }}</span> of <span class="font-medium text-slate-700">{{ $paginator->total() }}</span> results
        @else
            No results
        @endif
    </p>

    <div class="flex items-center gap-1.5">
        @if ($paginator->onFirstPage())
            <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-300">
                <x-icon name="chevron-right" class="h-4 w-4 rotate-180" />
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:bg-slate-50" aria-label="{{ __('pagination.previous') }}">
                <x-icon name="chevron-right" class="h-4 w-4 rotate-180" />
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-1 text-sm text-slate-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 text-sm font-semibold text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-sm font-semibold text-slate-600 transition hover:bg-slate-50" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:bg-slate-50" aria-label="{{ __('pagination.next') }}">
                <x-icon name="chevron-right" class="h-4 w-4" />
            </a>
        @else
            <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-300">
                <x-icon name="chevron-right" class="h-4 w-4" />
            </span>
        @endif
    </div>
</div>
