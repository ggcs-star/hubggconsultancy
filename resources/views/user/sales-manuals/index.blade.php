<x-layout title="Sales Manuals">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">
            Sales Manuals
        </h1>

        <p class="mt-1 text-sm text-secondary">
            Access the sales resources uploaded by your admin team.
        </p>
    </div>


    {{-- Search --}}
    <form
        method="GET"
        action="{{ route('user.manuals') }}"
        class="mt-6 rounded-xl border border-app-border bg-white p-4">

        <div class="flex flex-col gap-3 sm:flex-row">

            <div class="relative flex-1">

                <x-icon
                    name="search"
                    class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search uploaded files..."
                    class="w-full rounded-lg border-app-border py-2.5 pl-9 pr-3 text-sm shadow-sm focus:border-primary focus:ring-primary">

            </div>


            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90">

                <x-icon
                    name="search"
                    class="h-4 w-4" />

                Search

            </button>

        </div>

    </form>


    {{-- Uploaded Resources --}}
    <div class="mt-6">

        @if ($manuals->count())

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">

                @foreach ($manuals as $manual)

                    @foreach ($manual->attachments as $attachment)

                        @php
                            $extension = strtolower($attachment->file_type);

                            $icon = match ($extension) {
                                'pdf' => 'file-text',

                                'doc',
                                'docx' => 'file-text',

                                'xls',
                                'xlsx',
                                'csv' => 'table',

                                'ppt',
                                'pptx' => 'presentation',

                                'jpg',
                                'jpeg',
                                'png',
                                'webp',
                                'gif' => 'image',

                                'zip',
                                'rar',
                                '7z' => 'archive',

                                default => 'file',
                            };

                            $iconBg = match ($extension) {
                                'pdf' => 'bg-red-50 text-red-600',

                                'doc',
                                'docx' => 'bg-blue-50 text-blue-600',

                                'xls',
                                'xlsx',
                                'csv' => 'bg-green-50 text-green-600',

                                'ppt',
                                'pptx' => 'bg-orange-50 text-orange-600',

                                'jpg',
                                'jpeg',
                                'png',
                                'webp',
                                'gif' => 'bg-purple-50 text-purple-600',

                                'zip',
                                'rar',
                                '7z' => 'bg-amber-50 text-amber-600',

                                default => 'bg-slate-50 text-slate-600',
                            };
                        @endphp


                        <div
                            class="overflow-hidden rounded-xl border border-app-border bg-white transition hover:-translate-y-0.5 hover:shadow-md">


                            {{-- Image Preview --}}
                            @if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']))

                                <a
                                    href="{{ asset('storage/' . $attachment->file_path) }}"
                                    target="_blank"
                                    class="block">

                                    <img
                                        src="{{ asset('storage/' . $attachment->file_path) }}"
                                        alt="{{ $attachment->file_name }}"
                                        class="h-56 w-full object-cover">

                                </a>

                            @else

                                {{-- File Preview --}}
                                <div
                                    class="flex h-56 items-center justify-center bg-surface-alt">

                                    <div
                                        class="flex h-20 w-20 items-center justify-center rounded-2xl {{ $iconBg }}">

                                        <x-icon
                                            name="{{ $icon }}"
                                            class="h-10 w-10" />

                                    </div>

                                </div>

                            @endif


                            {{-- File Information --}}
                            <div class="p-5">

                                <div class="flex items-start gap-3">

                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $iconBg }}">

                                        <x-icon
                                            name="{{ $icon }}"
                                            class="h-5 w-5" />

                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <p
                                            class="truncate text-sm font-semibold text-secondary-dark"
                                            title="{{ $attachment->file_name }}">

                                            {{ $attachment->file_name }}

                                        </p>

                                        <p class="mt-1 text-xs uppercase text-secondary">

                                            {{ $attachment->file_type }}

                                            @if ($attachment->file_size)
                                                ·
                                                {{ number_format($attachment->file_size / 1024 / 1024, 2) }}
                                                MB
                                            @endif

                                        </p>

                                    </div>

                                </div>


                                {{-- Actions --}}
                                <div class="mt-5 flex gap-2">

                                    <a
                                        href="{{ asset('storage/' . $attachment->file_path) }}"
                                        target="_blank"
                                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90">

                                        <x-icon
                                            name="eye"
                                            class="h-4 w-4" />

                                        View

                                    </a>


                                    <a
                                        href="{{ asset('storage/' . $attachment->file_path) }}"
                                        download="{{ $attachment->file_name }}"
                                        class="inline-flex items-center justify-center rounded-lg border border-app-border bg-white px-4 py-2.5 text-sm font-medium text-secondary-dark transition hover:bg-surface-alt"
                                        title="Download">

                                        <x-icon
                                            name="download"
                                            class="h-4 w-4" />

                                    </a>

                                </div>

                            </div>

                        </div>

                    @endforeach

                @endforeach

            </div>


            {{-- Pagination --}}
            @if ($manuals->hasPages())

                <div class="mt-6">
                    {{ $manuals->links() }}
                </div>

            @endif


        @else

            {{-- Empty State --}}
            <div class="rounded-xl border border-app-border bg-white px-6 py-16 text-center">

                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-primary-light">

                    <x-icon
                        name="document"
                        class="h-7 w-7 text-primary" />

                </div>


                <h3 class="mt-4 text-base font-semibold text-secondary-dark">
                    No Resources Available
                </h3>


                <p class="mx-auto mt-1 max-w-md text-sm text-secondary">
                    Uploaded sales resources will appear here.
                </p>

            </div>

        @endif

    </div>

</x-layout>