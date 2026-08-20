<x-layout title="Documents" title-icon="document" subtitle="Guides, plans and presentations users can open in one click">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="w-full sm:max-w-sm">
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search documents..." class="form-input pl-10 {{ request('search') ? 'pr-9' : '' }}">
                @if (request('search'))
                    <a href="{{ route('admin.documents.index') }}" title="Clear search" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <x-icon name="x" class="h-4 w-4" />
                    </a>
                @endif
            </div>
        </form>

        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-document')" class="btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Add Document
        </button>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($documents as $document)
            <div class="card flex flex-col overflow-hidden border-l-4 border-l-brand-600 p-5">
                <div class="flex items-start justify-between gap-2">
                    <p class="font-bold text-slate-800">{{ $document->title }}</p>
                    <div class="flex shrink-0 items-center gap-1">
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-document-{{ $document->id }}')" title="Edit" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                            <x-icon name="pencil" class="h-4 w-4" />
                        </button>
                        <form method="POST" action="{{ route('admin.documents.destroy', $document) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete \'{{ $document->title }}\'?', target: $el })">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                </div>

                <p class="mt-2 flex-1 text-sm text-slate-500 line-clamp-3">{{ $document->description }}</p>

                <a href="{{ $document->url }}" target="_blank" rel="noopener" class="mt-3 truncate text-xs text-brand-700 hover:underline">{{ $document->url }}</a>

                <form method="POST" action="{{ route('admin.documents.publish.toggle', $document) }}" class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_published" value="{{ $document->is_published ? '0' : '1' }}">
                    <span class="flex items-center gap-1.5 text-xs font-medium text-slate-400">
                        <x-icon name="eye" class="h-3.5 w-3.5" />
                        {{ $document->is_published ? 'Visible to users' : 'Hidden' }}
                    </span>
                    <button type="submit" class="badge {{ $document->is_published ? 'badge-green' : 'badge-slate' }}">
                        {{ $document->is_published ? 'Published' : 'Draft' }}
                    </button>
                </form>
            </div>

            <x-modal name="edit-document-{{ $document->id }}" :show="false" max-width="lg">
                @include('admin.documents._form', ['document' => $document])
            </x-modal>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400">
                No documents yet. Click "Add Document" to create the first one.
            </div>
        @endforelse
    </div>

    <x-modal name="add-document" :show="$errors->isNotEmpty()" max-width="lg">
        @include('admin.documents._form', ['document' => null])
    </x-modal>

</x-layout>
