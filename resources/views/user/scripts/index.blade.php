<x-layout title="Scripts & Objection Handling" title-icon="book-open" subtitle="Pick a topic to watch a script in action and read the playbook">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="w-full sm:max-w-sm">
            <input type="hidden" name="language" value="{{ $language }}">
            @if ($type)
                <input type="hidden" name="type" value="{{ $type }}">
            @endif
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ $search }}" placeholder="Search topics..." class="form-input pl-10 {{ $search ? 'pr-9' : '' }}">
                @if ($search)
                    <a href="{{ route('user.scripts.index', ['language' => $language, 'type' => $type]) }}" title="Clear search" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <x-icon name="x" class="h-4 w-4" />
                    </a>
                @endif
            </div>
        </form>

        <div class="flex flex-wrap items-center justify-end gap-2">
            <a href="{{ route('user.scripts.index', ['language' => $language, 'search' => $search]) }}" class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ ! $type ? 'bg-brand-700 text-white shadow-sm' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                All
            </a>
            <a href="{{ route('user.scripts.index', ['language' => $language, 'search' => $search, 'type' => 'video']) }}" class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ $type === 'video' ? 'bg-brand-700 text-white shadow-sm' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                Videos
            </a>
            <a href="{{ route('user.scripts.index', ['language' => $language, 'search' => $search, 'type' => 'document']) }}" class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ $type === 'document' ? 'bg-brand-700 text-white shadow-sm' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                Documents
            </a>
        </div>
    </div>

    {{-- Language switch — only languages with at least one published script item get a tab. --}}
    @if (count($availableLanguages) >= 1)
        <div class="mt-4 flex items-center justify-end gap-2">
            @foreach ($availableLanguages as $value)
                <a href="{{ route('user.scripts.index', ['language' => $value, 'search' => $search, 'type' => $type]) }}" class="rounded-lg px-5 py-2 text-sm font-semibold transition {{ $language === $value ? 'bg-brand-700 text-white shadow-sm' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                    {{ ucfirst($value) }}
                </a>
            @endforeach
        </div>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($topics as $topic)
            @php
                $videos = $topic->items->where('type', 'video')->values();
                $documents = $topic->items->where('type', 'document')->values();
                $startLink = $videos->first() ?? $documents->first();
            @endphp

            <div class="card flex flex-col overflow-hidden border-l-4 border-l-brand-600" x-data="{ tab: '{{ $type === 'document' ? 'documents' : 'videos' }}' }">
                <div class="p-5 pb-0">
                    <p class="font-bold text-slate-800">{{ $topic->title }}</p>
                </div>

                <div class="mt-4 flex items-center gap-1 px-5">
                    @if (! $type || $type === 'video')
                        <button
                            type="button"
                            x-on:click="tab = 'videos'"
                            class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                            :class="tab === 'videos' ? 'bg-brand-50 text-brand-700' : 'text-slate-400 hover:text-slate-600'"
                        >
                            🎬 Videos <span class="rounded-full bg-black/5 px-1.5 py-0.5 text-[10px]">{{ $videos->count() }}</span>
                        </button>
                    @endif
                    @if (! $type || $type === 'document')
                        <button
                            type="button"
                            x-on:click="tab = 'documents'"
                            class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                            :class="tab === 'documents' ? 'bg-brand-50 text-brand-700' : 'text-slate-400 hover:text-slate-600'"
                        >
                            📄 Documents <span class="rounded-full bg-black/5 px-1.5 py-0.5 text-[10px]">{{ $documents->count() }}</span>
                        </button>
                    @endif
                </div>

                <div class="flex-1 px-5 py-4">
                    <div x-show="tab === 'videos'" x-cloak class="space-y-1">
                        @forelse ($videos as $video)
                            <a href="{{ route('user.scripts.items.open', $video) }}" target="_blank" rel="noopener" class="flex items-center justify-between gap-2 rounded-lg px-2 py-2 text-sm text-slate-600 transition hover:bg-slate-50 hover:text-brand-700">
                                <span class="min-w-0 truncate">{{ $video->title }}</span>
                                <x-icon name="play-circle" class="h-4 w-4 shrink-0 text-slate-300" />
                            </a>
                        @empty
                            <p class="px-2 py-2 text-sm text-slate-400">No videos for this topic yet.</p>
                        @endforelse
                    </div>

                    <div x-show="tab === 'documents'" x-cloak class="space-y-1">
                        @forelse ($documents as $document)
                            <a href="{{ route('user.scripts.items.open', $document) }}" target="_blank" rel="noopener" class="flex items-center gap-2 rounded-lg px-2 py-2 text-sm text-slate-600 transition hover:bg-slate-50 hover:text-brand-700">
                                @if ($document->thumbnailUrl())
                                    <img src="{{ $document->thumbnailUrl() }}" alt="" class="h-7 w-7 shrink-0 rounded-md object-cover">
                                @else
                                    <x-icon name="document" class="h-4 w-4 shrink-0 text-slate-300" />
                                @endif
                                <span class="min-w-0 flex-1 truncate">{{ $document->title }}</span>
                            </a>
                        @empty
                            <p class="px-2 py-2 text-sm text-slate-400">No documents for this topic yet.</p>
                        @endforelse
                    </div>
                </div>

                @if ($startLink)
                    <a href="{{ route('user.scripts.items.open', $startLink) }}" target="_blank" rel="noopener" class="flex items-center gap-1.5 border-t border-slate-100 px-5 py-4 text-sm font-semibold text-brand-700 hover:text-brand-800">
                        Start Learning
                        <x-icon name="chevron-right" class="h-4 w-4" />
                    </a>
                @endif
            </div>
        @empty
            <div class="card col-span-full p-10 text-center text-sm text-slate-400">
                No {{ ucfirst($language) }} scripts or objection-handling topics have been added yet.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $topics->links() }}
    </div>

</x-layout>
