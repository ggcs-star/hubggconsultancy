<x-layout title="Sales Toolkit" title-icon="briefcase" subtitle="Scripts, decks and templates — click any card to open it">

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <input type="hidden" name="language" value="{{ $language }}">
            <div class="relative w-full sm:max-w-sm">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search toolkit items..." class="form-input pl-10">
            </div>

            <select name="category" class="form-input w-full sm:w-56" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn-primary shrink-0 sm:w-auto">
                <x-icon name="search" class="h-4 w-4" />
                Search
            </button>

            @if (request('search') || request('category'))
                <a href="{{ route('user.sales-toolkit.index', ['language' => $language]) }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Reset</a>
            @endif
        </form>

        {{-- Language switch — only languages with at least one toolkit item get a tab. --}}
        @if (count($availableLanguages) >= 1)
            <div class="flex items-center gap-2">
                @foreach ($availableLanguages as $value)
                    <a href="{{ route('user.sales-toolkit.index', ['language' => $value, 'search' => request('search'), 'category' => request('category')]) }}" class="rounded-lg px-5 py-2 text-sm font-semibold transition {{ $language === $value ? 'bg-brand-700 text-white shadow-sm' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                        {{ ucfirst($value) }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($items as $item)
            <a href="{{ route('user.sales-toolkit.open', $item) }}" target="_blank" rel="noopener"
                class="card flex flex-col border-l-4 border-l-brand-600 p-5 transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex min-w-0 items-center gap-3">
                        @if ($item->thumbnailUrl())
                            <img src="{{ $item->thumbnailUrl() }}" alt="" class="h-10 w-10 shrink-0 rounded-lg object-cover">
                        @else
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                                <x-icon name="briefcase" class="h-5 w-5" />
                            </span>
                        @endif
                        <p class="min-w-0 truncate font-bold text-slate-800">{{ $item->title }}</p>
                    </div>
                    @if ($item->category)
                        <span class="badge badge-slate shrink-0">{{ $item->category }}</span>
                    @endif
                </div>
                @if ($item->description)
                    <p class="mt-2 flex-1 text-sm text-slate-500">{{ $item->description }}</p>
                @endif
                <span class="mt-4 flex items-center gap-1.5 truncate text-xs font-semibold text-brand-700">
                    <x-icon name="document" class="h-3.5 w-3.5 shrink-0" />
                    {{ $item->original_filename ?? 'Open file' }}
                </span>
            </a>
        @empty
            <div class="card col-span-full p-10 text-center text-sm text-slate-400">
                @if (request('search') || request('category'))
                    No toolkit items match your search or filter.
                @else
                    No {{ ucfirst($language) }} toolkit items have been added yet.
                @endif
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $items->links() }}
    </div>

</x-layout>
