<x-layout title="Sales Toolkit" title-icon="briefcase" subtitle="Scripts, decks and templates salespeople can open in one click">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="flex w-full flex-col gap-3 sm:flex-row sm:items-center">
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

            <select name="language" class="form-input w-full sm:w-40" onchange="this.form.submit()">
                <option value="">All Languages</option>
                <option value="english" @selected(request('language') === 'english')>English</option>
                <option value="hindi" @selected(request('language') === 'hindi')>Hindi</option>
                <option value="gujarati" @selected(request('language') === 'gujarati')>Gujarati</option>
            </select>

            @if (request('search') || request('category') || request('language'))
                <a href="{{ route('admin.sales-toolkit.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Reset</a>
            @endif
        </form>

        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-toolkit-item')" class="btn-primary shrink-0">
            <x-icon name="plus" class="h-4 w-4" />
            Add Item
        </button>
    </div>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">All Toolkit Items</h2>
            <p class="mt-0.5 text-xs text-slate-400">{{ $items->total() }} total item{{ $items->total() === 1 ? '' : 's' }}</p>
        </div>

        @if ($items->count())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-400">
                            <th class="px-5 py-3 font-semibold">Item</th>
                            <th class="px-5 py-3 font-semibold">Category</th>
                            <th class="px-5 py-3 font-semibold">Language</th>
                            <th class="px-5 py-3 font-semibold">File</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold">Sort Order</th>
                            <th class="px-5 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($items as $item)
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($item->thumbnailUrl())
                                            <img src="{{ $item->thumbnailUrl() }}" alt="" class="h-10 w-10 shrink-0 rounded-lg object-cover">
                                        @else
                                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                                                <x-icon name="briefcase" class="h-5 w-5" />
                                            </span>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-slate-800">{{ $item->title }}</p>
                                            @if ($item->description)
                                                <p class="mt-0.5 max-w-xs truncate text-xs text-slate-400">{{ $item->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    @if ($item->category)
                                        <span class="badge badge-slate">{{ $item->category }}</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <span class="badge badge-slate">{{ ucfirst($item->language) }}</span>
                                </td>

                                <td class="px-5 py-4">
                                    <a href="{{ $item->fileUrl() }}" target="_blank" rel="noopener" class="inline-flex max-w-[180px] items-center gap-1.5 text-brand-700 hover:underline">
                                        <x-icon name="document" class="h-4 w-4 shrink-0" />
                                        <span class="truncate">{{ $item->original_filename ?? 'File' }}</span>
                                    </a>
                                </td>

                                <td class="px-5 py-4">
                                    @if ($item->is_published)
                                        <span class="badge badge-green">
                                            <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                            Published
                                        </span>
                                    @else
                                        <span class="badge badge-slate">
                                            <x-icon name="document" class="h-3.5 w-3.5" />
                                            Draft
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-slate-500">{{ $item->sort_order }}</td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ $item->fileUrl() }}" target="_blank" rel="noopener" title="Preview" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-600 transition hover:bg-blue-100">
                                            <x-icon name="eye" class="h-4 w-4" />
                                        </a>

                                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-toolkit-item-{{ $item->id }}')" title="Edit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-violet-200 bg-violet-50 text-violet-600 transition hover:bg-violet-100">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                        </button>

                                        <form method="POST" action="{{ route('admin.sales-toolkit.publish.toggle', $item) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_published" value="{{ $item->is_published ? '0' : '1' }}">
                                            <button type="submit" title="{{ $item->is_published ? 'Move to Draft' : 'Publish' }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-green-200 bg-green-50 text-green-600 transition hover:bg-green-100">
                                                <x-icon name="{{ $item->is_published ? 'eye-off' : 'check-circle' }}" class="h-4 w-4" />
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.sales-toolkit.destroy', $item) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete \'{{ $item->title }}\'?', target: $el })">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100">
                                                <x-icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <x-modal name="edit-toolkit-item-{{ $item->id }}" :show="false" max-width="lg">
                                @include('admin.sales-toolkit._form', ['item' => $item, 'categories' => $categories])
                            </x-modal>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($items->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $items->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                    <x-icon name="briefcase" class="h-7 w-7 text-brand-600" />
                </div>
                <h3 class="mt-4 font-bold text-slate-800">No toolkit items found</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">
                    @if (request('search') || request('category'))
                        Try changing your search or filters.
                    @else
                        Click "Add Item" to create the first one.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <x-modal name="add-toolkit-item" :show="$errors->isNotEmpty()" max-width="lg">
        @include('admin.sales-toolkit._form', ['item' => null, 'categories' => $categories])
    </x-modal>

</x-layout>
