<x-layout title="Scripts & Objection Handling" title-icon="book-open" subtitle="Group videos and documents into topics for the sales team">

    <div class="flex justify-end">
        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-topic')" class="btn-primary shrink-0">
            <x-icon name="plus" class="h-4 w-4" />
            Add Topic
        </button>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($topics as $topic)
            <div class="card flex flex-col p-5">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                            <x-icon name="book-open" class="h-5 w-5" />
                        </span>
                        <p class="font-bold text-slate-800">{{ $topic->title }}</p>
                    </div>

                    <div class="flex shrink-0 items-center gap-1">
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-topic-{{ $topic->id }}')" title="Edit" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                            <x-icon name="pencil" class="h-4 w-4" />
                        </button>
                        <form method="POST" action="{{ route('admin.scripts.destroy', $topic) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete \'{{ $topic->title }}\' and all its items? This cannot be undone.', target: $el })">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                </div>

                <p class="mt-3 flex-1 text-xs text-slate-400">{{ $topic->items_count }} {{ Str::plural('item', $topic->items_count) }}</p>

                <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
                    <a href="{{ route('admin.scripts.show', $topic) }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Manage Items</a>

                    <form method="POST" action="{{ route('admin.scripts.publish.toggle', $topic) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="is_published" value="{{ $topic->is_published ? '0' : '1' }}">
                        <button type="submit" class="badge {{ $topic->is_published ? 'badge-green' : 'badge-slate' }}">
                            {{ $topic->is_published ? 'Published' : 'Draft' }}
                        </button>
                    </form>
                </div>
            </div>

            <x-modal name="edit-topic-{{ $topic->id }}" :show="false" max-width="lg">
                @include('admin.scripts._form', ['topic' => $topic])
            </x-modal>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400">
                No topics yet. Click "Add Topic" to create the first one, e.g. "Price Objection" or "First Call".
            </div>
        @endforelse
    </div>

    <x-modal name="add-topic" :show="$errors->isNotEmpty()" max-width="lg">
        @include('admin.scripts._form', ['topic' => null])
    </x-modal>

</x-layout>
