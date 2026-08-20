<x-layout title="Documents" title-icon="document" subtitle="Guides, plans and presentations — click any card to open it">

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($documents as $document)
            <a href="{{ $document->url }}" target="_blank" rel="noopener"
                class="card flex flex-col border-l-4 border-l-brand-600 p-5 transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="font-bold text-slate-800">{{ $document->title }}</p>
                @if ($document->description)
                    <p class="mt-2 flex-1 text-sm text-slate-500">{{ $document->description }}</p>
                @endif
                <span class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700">
                    <x-icon name="external-link" class="h-3.5 w-3.5" />
                    Open document
                </span>
            </a>
        @empty
            <div class="card col-span-full p-10 text-center text-sm text-slate-400">No documents have been added yet.</div>
        @endforelse
    </div>

</x-layout>
