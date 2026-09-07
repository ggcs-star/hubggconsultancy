<x-layout title="Announcements" title-icon="bell" subtitle="Updates from your admin">

    <div class="card">
        @if ($announcements->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                    <x-icon name="bell" class="h-7 w-7 text-brand-600" />
                </div>
                <h3 class="mt-4 font-bold text-slate-800">No announcements yet</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">Check back later for updates from your admin.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($announcements as $announcement)
                    <div class="flex items-start gap-3 px-5 py-4">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                            <x-icon name="bell" class="h-4 w-4" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-800">{{ $announcement->title }}</p>
                            @if ($announcement->body)
                                <div
                                    x-data="{ expanded: false, overflowing: false }"
                                    x-init="$nextTick(() => overflowing = $refs.body.scrollHeight > $refs.body.clientHeight + 1)"
                                >
                                    <p x-ref="body" class="mt-0.5 text-sm text-slate-500 whitespace-pre-line" :class="expanded ? '' : 'line-clamp-3'">{{ $announcement->body }}</p>
                                    <button
                                        type="button"
                                        x-show="overflowing"
                                        x-cloak
                                        x-on:click="expanded = !expanded"
                                        class="mt-1 text-xs font-semibold text-brand-700 hover:text-brand-800"
                                        x-text="expanded ? 'Read less' : 'Read more'"
                                    ></button>
                                </div>
                            @endif
                            <p class="mt-1 text-xs text-slate-400">{{ $announcement->published_at->format('d M Y') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($announcements->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $announcements->links() }}
                </div>
            @endif
        @endif
    </div>

</x-layout>
