<x-layout title="Documents" title-icon="document" subtitle="Guides, plans and presentations — click any card to open it">

    <form method="GET" class="w-full sm:max-w-sm">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search documents..." class="form-input pl-10 {{ request('search') ? 'pr-9' : '' }}">
            @if (request('search'))
                <a href="{{ route('user.documents.index') }}" title="Clear search" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <x-icon name="x" class="h-4 w-4" />
                </a>
            @endif
        </div>
    </form>

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($documents as $document)
            <a href="{{ $document->url }}" target="_blank" rel="noopener"
                class="card flex flex-col overflow-hidden transition hover:-translate-y-0.5 hover:shadow-md">
                @if ($document->thumbnailUrl())
                    <img src="{{ $document->thumbnailUrl() }}" alt="" class="h-40 w-full object-cover">
                @else
                    <div class="flex h-40 w-full items-center justify-center bg-slate-100 text-slate-300">
                        <x-icon name="document" class="h-10 w-10" />
                    </div>
                @endif

                <div class="flex flex-1 flex-col p-5" x-data="{ expanded: false }">
                    <p class="font-bold text-slate-800">{{ $document->title }}</p>
                    @if ($document->description)
                        <p class="mt-2 flex-1 text-sm text-slate-500" :class="expanded ? '' : 'line-clamp-3'">{{ $document->description }}</p>
                        <button type="button" x-on:click.prevent.stop="expanded = !expanded" class="mt-1 self-start text-xs font-semibold text-brand-700 hover:underline" x-text="expanded ? 'Read less' : 'Read more'"></button>
                    @endif
                    <span class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700">
                        <x-icon name="external-link" class="h-3.5 w-3.5" />
                        Open document
                    </span>
                </div>
            </a>
        @empty
            <div class="card col-span-full p-10 text-center text-sm text-slate-400">
                @if (request('search'))
                    No documents match your search.
                @else
                    No documents have been added yet.
                @endif
            </div>
        @endforelse
    </div>

    @if ($documents->hasPages())
        <div class="mt-6">
            {{ $documents->links() }}
        </div>
    @endif

</x-layout>
