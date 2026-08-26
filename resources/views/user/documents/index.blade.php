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
                class="card flex flex-col border-l-4 border-l-brand-600 p-5 transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center gap-3">
                    @if ($document->thumbnailUrl())
                        <img src="{{ $document->thumbnailUrl() }}" alt="" class="h-11 w-11 shrink-0 rounded-lg object-cover">
                    @else
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                            <x-icon name="document" class="h-5 w-5" />
                        </span>
                    @endif
                    <p class="min-w-0 font-bold text-slate-800">{{ $document->title }}</p>
                </div>
                @if ($document->description)
                    <p class="mt-2 flex-1 text-sm text-slate-500">{{ $document->description }}</p>
                @endif
                <span class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700">
                    <x-icon name="external-link" class="h-3.5 w-3.5" />
                    Open document
                </span>
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
