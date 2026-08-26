<x-layout title="Onboarding Checklist" title-icon="check-circle" subtitle="Steps every new salesperson works through">

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.onboarding-checklist.progress') }}" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">
            <x-icon name="users" class="h-4 w-4" />
            View Progress
        </a>
        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-checklist-item')" class="btn-primary shrink-0">
            <x-icon name="plus" class="h-4 w-4" />
            Add Checklist Item
        </button>
    </div>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Checklist Steps</h2>
            <p class="mt-0.5 text-xs text-slate-400">{{ $items->count() }} step{{ $items->count() === 1 ? '' : 's' }}</p>
        </div>

        @if ($items->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                    <x-icon name="check-circle" class="h-7 w-7 text-brand-600" />
                </div>
                <h3 class="mt-4 font-bold text-slate-800">No checklist steps yet</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">Click "Add Checklist Item" to create the first step.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($items as $item)
                    <div class="flex items-start justify-between gap-3 px-5 py-4">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-sm font-bold text-brand-600">
                                {{ $loop->iteration }}
                            </span>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $item->title }}</p>
                                @if ($item->description)
                                    <p class="mt-0.5 text-sm text-slate-500">{{ $item->description }}</p>
                                @endif
                                <div class="mt-1.5 flex flex-wrap items-center gap-3">
                                    @if ($item->link)
                                        <a href="{{ $item->link }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-xs font-semibold text-brand-700 hover:underline">
                                            <x-icon name="external-link" class="h-3 w-3" />
                                            Open Link
                                        </a>
                                    @endif
                                    <span class="text-xs text-slate-400">{{ $item->completions_count }} {{ Str::plural('person', $item->completions_count) }} completed</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-1">
                            <form method="POST" action="{{ route('admin.onboarding-checklist.publish.toggle', $item) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_published" value="{{ $item->is_published ? '0' : '1' }}">
                                <button type="submit" class="badge {{ $item->is_published ? 'badge-green' : 'badge-slate' }}">
                                    {{ $item->is_published ? 'Published' : 'Draft' }}
                                </button>
                            </form>
                            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-checklist-item-{{ $item->id }}')" title="Edit" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </button>
                            <form method="POST" action="{{ route('admin.onboarding-checklist.destroy', $item) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete \'{{ $item->title }}\'?', target: $el })">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                    <x-icon name="trash" class="h-4 w-4" />
                                </button>
                            </form>
                        </div>
                    </div>

                    <x-modal name="edit-checklist-item-{{ $item->id }}" :show="false" max-width="lg">
                        @include('admin.onboarding-checklist._form', ['item' => $item])
                    </x-modal>
                @endforeach
            </div>
        @endif
    </div>

    <x-modal name="add-checklist-item" :show="$errors->isNotEmpty()" max-width="lg">
        @include('admin.onboarding-checklist._form', ['item' => null])
    </x-modal>

</x-layout>
