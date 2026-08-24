<x-layout title="Sales Toolkit" title-icon="briefcase" subtitle="Scripts, decks and templates — click any card to open it">

    <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center">
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
            <a href="{{ route('user.sales-toolkit.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Reset</a>
        @endif
    </form>

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($items as $item)
            <a href="{{ $item->fileUrl() }}" target="_blank" rel="noopener"
                class="card flex flex-col border-l-4 border-l-brand-600 p-5 transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-start justify-between gap-2">
                    <p class="font-bold text-slate-800">{{ $item->title }}</p>
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
                    No toolkit items have been added yet.
                @endif
            </div>
        @endforelse
    </div>

</x-layout>
