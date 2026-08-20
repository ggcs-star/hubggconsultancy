<x-layout title="Resources" title-icon="video" subtitle="Video library with Hindi/English links and in-video quizzes">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="w-full sm:max-w-sm">
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search resources..." class="form-input pl-10 {{ request('search') ? 'pr-9' : '' }}">
                @if (request('search'))
                    <a href="{{ route('admin.resources.index') }}" title="Clear search" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <x-icon name="x" class="h-4 w-4" />
                    </a>
                @endif
            </div>
        </form>

        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-resource')" class="btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Add Resource
        </button>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($resources as $resource)
            <div class="card flex flex-col overflow-hidden">
                @if ($resource->thumbnail)
                    <img src="{{ asset('storage/' . $resource->thumbnail) }}" alt="" class="h-40 w-full object-cover">
                @else
                    <div class="flex h-40 w-full items-center justify-center bg-slate-100 text-slate-300">
                        <x-icon name="video" class="h-10 w-10" />
                    </div>
                @endif

                <div class="flex flex-1 flex-col p-5">
                    <div class="flex items-start justify-between gap-2">
                        <p class="font-bold text-slate-800">{{ $resource->title }}</p>
                        <div class="flex shrink-0 items-center gap-1">
                            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-resource-{{ $resource->id }}')" title="Edit" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </button>
                            <form method="POST" action="{{ route('admin.resources.destroy', $resource) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete \'{{ $resource->title }}\' and all its checkpoints? This cannot be undone.', target: $el })">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                    <x-icon name="trash" class="h-4 w-4" />
                                </button>
                            </form>
                        </div>
                    </div>

                    <p class="mt-2 flex-1 text-sm text-slate-500 line-clamp-3">{{ $resource->description }}</p>

                    <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs text-slate-400">
                        <span>{{ $resource->checkpoints_count }} {{ Str::plural('checkpoint', $resource->checkpoints_count) }}</span>
                        @if ($resource->hindi_youtube_url)<span class="badge badge-slate">Hindi</span>@endif
                        @if ($resource->english_youtube_url)<span class="badge badge-slate">English</span>@endif
                    </div>

                    <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
                        <a href="{{ route('admin.resources.show', $resource) }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Manage Checkpoints</a>

                        <form method="POST" action="{{ route('admin.resources.publish.toggle', $resource) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_published" value="{{ $resource->is_published ? '0' : '1' }}">
                            <button type="submit" class="badge {{ $resource->is_published ? 'badge-green' : 'badge-slate' }}">
                                {{ $resource->is_published ? 'Published' : 'Draft' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <x-modal name="edit-resource-{{ $resource->id }}" :show="false" max-width="lg">
                @include('admin.resources._form', ['resource' => $resource])
            </x-modal>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400">
                No resources yet. Click "Add Resource" to create the first one.
            </div>
        @endforelse
    </div>

    <x-modal name="add-resource" :show="$errors->isNotEmpty()" max-width="lg">
        @include('admin.resources._form', ['resource' => null])
    </x-modal>

</x-layout>
