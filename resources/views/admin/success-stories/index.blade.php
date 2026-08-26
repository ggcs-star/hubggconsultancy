<x-layout title="Success Stories" title-icon="lightbulb" subtitle="Show how training helped someone improve">

    <div class="flex justify-end">
        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-success-story')" class="btn-primary shrink-0">
            <x-icon name="plus" class="h-4 w-4" />
            Add Success Story
        </button>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
        @forelse ($successStories as $successStory)
            <div class="card flex flex-col p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        @if ($successStory->photoUrl())
                            <img src="{{ $successStory->photoUrl() }}" alt="" class="h-11 w-11 shrink-0 rounded-full object-cover">
                        @else
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                                <x-icon name="lightbulb" class="h-5 w-5" />
                            </span>
                        @endif
                        <div>
                            <p class="font-bold text-slate-800">{{ $successStory->name }}</p>
                            @if ($successStory->designation)
                                <p class="text-sm text-brand-600">{{ $successStory->designation }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-1">
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-success-story-{{ $successStory->id }}')" title="Edit" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                            <x-icon name="pencil" class="h-4 w-4" />
                        </button>
                        <form method="POST" action="{{ route('admin.success-stories.destroy', $successStory) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete this success story?', target: $el })">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                </div>

                @if ($successStory->headline)
                    <p class="mt-3 font-bold text-slate-800">{{ $successStory->headline }}</p>
                @endif

                <p class="mt-2 flex-1 text-sm italic text-slate-500 line-clamp-3">&ldquo;{{ $successStory->testimonial }}&rdquo;</p>

                @if (!empty($successStory->metrics))
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($successStory->metrics as $metric)
                            <span class="badge badge-slate">
                                {{ $metric['label'] }}: {{ $metric['before'] ?? '—' }} → {{ $metric['after'] ?? '—' }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.success-stories.active.toggle', $successStory) }}" class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_active" value="{{ $successStory->is_active ? '0' : '1' }}">
                    <span class="flex items-center gap-1.5 text-xs font-medium text-slate-400">
                        <x-icon name="eye" class="h-3.5 w-3.5" />
                        {{ $successStory->is_active ? 'Visible' : 'Hidden' }}
                    </span>
                    <button type="submit" class="badge {{ $successStory->is_active ? 'badge-green' : 'badge-slate' }}">
                        {{ $successStory->is_active ? 'Active' : 'Inactive' }}
                    </button>
                </form>
            </div>

            <x-modal name="edit-success-story-{{ $successStory->id }}" :show="false" max-width="lg">
                @include('admin.success-stories._form', ['successStory' => $successStory])
            </x-modal>
        @empty
            <div class="col-span-full card px-6 py-16 text-center text-slate-400">
                No success stories yet. Click "Add Success Story" to share the first one.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $successStories->links() }}
    </div>

    <x-modal name="add-success-story" :show="$errors->isNotEmpty()" max-width="lg">
        @include('admin.success-stories._form', ['successStory' => null])
    </x-modal>

</x-layout>
