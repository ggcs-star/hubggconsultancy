@php
    $videos = $topic->items->where('type', 'video')->sortBy('sort_order')->values();
    $documents = $topic->items->where('type', 'document')->sortBy('sort_order')->values();
@endphp

<x-layout title="Manage Items" title-icon="book-open" subtitle="{{ $topic->title }}">

    <a href="{{ route('admin.scripts.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
        <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
        Back to Topics
    </a>

    <div class="mt-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                <x-icon name="book-open" class="h-6 w-6" />
            </span>
            <p class="text-lg font-bold text-slate-800">{{ $topic->title }}</p>
        </div>

        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-item')" class="btn-primary shrink-0">
            <x-icon name="plus" class="h-4 w-4" />
            Add Item
        </button>
    </div>

    @foreach ([['label' => 'Videos', 'icon' => 'video', 'items' => $videos], ['label' => 'Documents', 'icon' => 'document', 'items' => $documents]] as $group)
        <div class="mt-6 card">
            <div class="flex items-center gap-2 border-b border-slate-100 px-5 py-4">
                <x-icon name="{{ $group['icon'] }}" class="h-4 w-4 text-slate-400" />
                <h2 class="font-bold text-slate-800">{{ $group['label'] }}</h2>
                <span class="badge badge-slate">{{ $group['items']->count() }}</span>
            </div>

            @forelse ($group['items'] as $item)
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 last:border-b-0">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <p class="truncate font-semibold text-slate-800">{{ $item->title }}</p>
                            @if (! $item->is_published)
                                <span class="badge badge-slate shrink-0">Draft</span>
                            @endif
                            @if ($item->is_external)
                                <span class="badge badge-slate shrink-0">Link</span>
                            @endif
                        </div>
                        <a href="{{ $item->fileUrl() }}" target="_blank" rel="noopener" class="mt-0.5 inline-flex max-w-full items-center gap-1.5 truncate text-xs text-brand-700 hover:underline">
                            {{ $item->original_filename ?? ($item->is_external ? $item->url : 'View file') }}
                        </a>
                    </div>

                    <div class="flex shrink-0 items-center gap-1">
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-item-{{ $item->id }}')" title="Edit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-violet-200 bg-violet-50 text-violet-600 transition hover:bg-violet-100">
                            <x-icon name="pencil" class="h-4 w-4" />
                        </button>

                        <form method="POST" action="{{ route('admin.script-items.publish.toggle', $item) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_published" value="{{ $item->is_published ? '0' : '1' }}">
                            <button type="submit" title="{{ $item->is_published ? 'Move to Draft' : 'Publish' }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-green-200 bg-green-50 text-green-600 transition hover:bg-green-100">
                                <x-icon name="{{ $item->is_published ? 'eye-off' : 'check-circle' }}" class="h-4 w-4" />
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.script-items.destroy', $item) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete \'{{ $item->title }}\'?', target: $el })">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                </div>

                <x-modal name="edit-item-{{ $item->id }}" :show="false" max-width="lg">
                    @include('admin.scripts._item-form', ['item' => $item, 'topic' => $topic])
                </x-modal>
            @empty
                <p class="px-5 py-8 text-center text-sm text-slate-400">No {{ strtolower($group['label']) }} yet.</p>
            @endforelse
        </div>
    @endforeach

    <x-modal name="add-item" :show="$errors->isNotEmpty()" max-width="lg">
        @include('admin.scripts._item-form', ['item' => null, 'topic' => $topic])
    </x-modal>

</x-layout>
