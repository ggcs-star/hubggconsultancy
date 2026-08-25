<x-layout title="Announcements" title-icon="bell" subtitle="Post updates shown on every dashboard">

    <div class="flex justify-end">
        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-announcement')" class="btn-primary shrink-0">
            <x-icon name="plus" class="h-4 w-4" />
            Post Announcement
        </button>
    </div>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">All Announcements</h2>
            <p class="mt-0.5 text-xs text-slate-400">{{ $announcements->count() }} total</p>
        </div>

        @if ($announcements->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                    <x-icon name="bell" class="h-7 w-7 text-brand-600" />
                </div>
                <h3 class="mt-4 font-bold text-slate-800">No announcements yet</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">Click "Post Announcement" to share the first update.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($announcements as $announcement)
                    <div class="flex items-start justify-between gap-3 px-5 py-4">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                                <x-icon name="bell" class="h-4 w-4" />
                            </span>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $announcement->title }}</p>
                                @if ($announcement->body)
                                    <p class="mt-0.5 text-sm text-slate-500">{{ $announcement->body }}</p>
                                @endif
                                <p class="mt-1 text-xs text-slate-400">{{ $announcement->published_at->format('d M Y') }}</p>
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-1">
                            <form method="POST" action="{{ route('admin.announcements.active.toggle', $announcement) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_active" value="{{ $announcement->is_active ? '0' : '1' }}">
                                <button type="submit" class="badge {{ $announcement->is_active ? 'badge-green' : 'badge-slate' }}">
                                    {{ $announcement->is_active ? 'Active' : 'Hidden' }}
                                </button>
                            </form>
                            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-announcement-{{ $announcement->id }}')" title="Edit" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </button>
                            <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete this announcement?', target: $el })">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                    <x-icon name="trash" class="h-4 w-4" />
                                </button>
                            </form>
                        </div>
                    </div>

                    <x-modal name="edit-announcement-{{ $announcement->id }}" :show="false" max-width="lg">
                        @include('admin.announcements._form', ['announcement' => $announcement])
                    </x-modal>
                @endforeach
            </div>
        @endif
    </div>

    <x-modal name="add-announcement" :show="$errors->isNotEmpty()" max-width="lg">
        @include('admin.announcements._form', ['announcement' => null])
    </x-modal>

</x-layout>
