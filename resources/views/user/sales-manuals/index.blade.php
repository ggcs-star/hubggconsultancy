<x-layout title="Sales Manuals" subtitle="Access the sales resources uploaded by your admin team.">

    {{-- ========================================================= --}}
    {{-- LANGUAGE SWITCH --}}
    {{-- ========================================================= --}}

    {{-- Only languages with at least one published manual get a tab. --}}
    @if (count($availableLanguages) >= 1)
        <div class="flex items-center justify-end gap-2">
            @foreach ($availableLanguages as $value)
                <a href="{{ route('user.manuals', ['language' => $value, 'search' => request('search')]) }}" class="rounded-lg px-5 py-2 text-sm font-semibold transition {{ $language === $value ? 'bg-primary text-white shadow-sm' : 'bg-surface-alt text-secondary hover:bg-app-border/40' }}">
                    {{ ucfirst($value) }}
                </a>
            @endforeach
        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- SEARCH --}}
    {{-- ========================================================= --}}

    <form
        method="GET"
        action="{{ route('user.manuals') }}"
        class="mt-4 rounded-xl border border-app-border bg-white p-4"
    >

        <input type="hidden" name="language" value="{{ $language }}">

        <div class="flex flex-col gap-3 sm:flex-row">

            <div class="relative flex-1">

                <x-icon
                    name="search"
                    class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary"
                />

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search uploaded files..."
                    class="w-full rounded-lg border-app-border py-2.5 pl-9 pr-3 text-sm shadow-sm focus:border-primary focus:ring-primary"
                >

            </div>


            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90"
            >

                <x-icon
                    name="search"
                    class="h-4 w-4"
                />

                Search

            </button>

        </div>

    </form>


    {{-- ========================================================= --}}
    {{-- RESOURCES --}}
    {{-- ========================================================= --}}

    <div class="mt-6">

        @if ($manuals->count())

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">

                @foreach ($manuals as $manual)

                    {{-- ================================================= --}}
                    {{-- COVER IMAGE --}}
                    {{-- ================================================= --}}

                    @if (!empty($manual->cover_image))

                        @php
                            $coverUrl = asset(
                                'storage/' . ltrim($manual->cover_image, '/')
                            );
                        @endphp

                        <div
                            class="overflow-hidden rounded-xl border border-app-border bg-white transition hover:-translate-y-0.5 hover:shadow-md"
                        >

                            <a
                                href="{{ $coverUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="block"
                            >

                                <div class="flex h-56 w-full items-center justify-center overflow-hidden bg-slate-50">

                                    <img
                                        src="{{ $coverUrl }}"
                                        alt="{{ $manual->title }}"
                                        class="h-full w-full object-contain p-2"
                                        loading="lazy"
                                    >

                                </div>

                            </a>


                            <div class="p-5">

                                <div class="flex items-start gap-3">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-purple-50 text-purple-600">

                                        <x-icon
                                            name="image"
                                            class="h-5 w-5"
                                        />

                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <p
                                            class="truncate text-sm font-semibold text-secondary-dark"
                                            title="{{ $manual->title }}"
                                        >
                                            {{ $manual->title }}
                                        </p>

                                        <p class="mt-1 text-xs uppercase text-secondary">
                                            IMAGE
                                        </p>

                                    </div>

                                </div>


                                <div class="mt-5">

                                    <a
                                        href="{{ $coverUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90"
                                    >

                                        <x-icon
                                            name="eye"
                                            class="h-4 w-4"
                                        />

                                        View Image

                                    </a>

                                </div>

                            </div>

                        </div>

                    @endif


                    {{-- ================================================= --}}
                    {{-- ATTACHMENTS --}}
                    {{-- ================================================= --}}

                    @foreach ($manual->attachments as $attachment)

                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | Extension
                            |--------------------------------------------------------------------------
                            */

                            $extension = strtolower(
                                pathinfo(
                                    $attachment->file_name,
                                    PATHINFO_EXTENSION
                                )
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | File URL
                            |--------------------------------------------------------------------------
                            */

                            $fileUrl = asset(
                                'storage/' . ltrim(
                                    $attachment->file_path,
                                    '/'
                                )
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | File Types
                            |--------------------------------------------------------------------------
                            */

                            $isImage = in_array($extension, [
                                'jpg',
                                'jpeg',
                                'png',
                                'webp',
                                'gif',
                                'svg',
                            ]);


                            $isPdf = $extension === 'pdf';


                            /*
                            |--------------------------------------------------------------------------
                            | Icon
                            |--------------------------------------------------------------------------
                            */

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
                                'gif',
                                'svg' => 'image',

                                'zip',
                                'rar',
                                '7z' => 'archive',

                                default => 'file',

                            };


                            /*
                            |--------------------------------------------------------------------------
                            | Icon Background
                            |--------------------------------------------------------------------------
                            */

                            $iconBg = match ($extension) {

                                'pdf' =>
                                    'bg-red-50 text-red-600',

                                'doc',
                                'docx' =>
                                    'bg-blue-50 text-blue-600',

                                'xls',
                                'xlsx',
                                'csv' =>
                                    'bg-green-50 text-green-600',

                                'ppt',
                                'pptx' =>
                                    'bg-orange-50 text-orange-600',

                                'jpg',
                                'jpeg',
                                'png',
                                'webp',
                                'gif',
                                'svg' =>
                                    'bg-purple-50 text-purple-600',

                                'zip',
                                'rar',
                                '7z' =>
                                    'bg-amber-50 text-amber-600',

                                default =>
                                    'bg-slate-50 text-slate-600',

                            };

                        @endphp


                        {{-- ================================================= --}}
                        {{-- ATTACHMENT CARD --}}
                        {{-- ================================================= --}}

                        <div
                            class="overflow-hidden rounded-xl border border-app-border bg-white transition hover:-translate-y-0.5 hover:shadow-md"
                        >


                            {{-- ================================================= --}}
                            {{-- IMAGE PREVIEW --}}
                            {{-- ================================================= --}}

                            @if ($isImage)

                                <a
                                    href="{{ $fileUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="block"
                                >

                                    <div class="flex h-56 w-full items-center justify-center overflow-hidden bg-slate-50">

                                        <img
                                            src="{{ $fileUrl }}"
                                            alt="{{ $attachment->file_name }}"
                                            class="h-full w-full object-contain p-2"
                                            loading="lazy"
                                        >

                                    </div>

                                </a>


                            {{-- ================================================= --}}
                            {{-- PDF PREVIEW --}}
                            {{-- ================================================= --}}

                            @elseif ($isPdf)

                                <div class="h-56 w-full overflow-hidden bg-slate-100">

                                    <iframe
                                        src="{{ $fileUrl }}#toolbar=1&navpanes=0&scrollbar=1"
                                        class="h-full w-full border-0"
                                        loading="lazy"
                                        title="{{ $attachment->file_name }}"
                                    ></iframe>

                                </div>


                            {{-- ================================================= --}}
                            {{-- OTHER FILES --}}
                            {{-- ================================================= --}}

                            @else

                                <div
                                    class="flex h-56 items-center justify-center bg-surface-alt"
                                >

                                    <div
                                        class="flex h-20 w-20 items-center justify-center rounded-2xl {{ $iconBg }}"
                                    >

                                        <x-icon
                                            name="{{ $icon }}"
                                            class="h-10 w-10"
                                        />

                                    </div>

                                </div>

                            @endif


                            {{-- ================================================= --}}
                            {{-- FILE INFORMATION --}}
                            {{-- ================================================= --}}

                            <div class="p-5">

                                <div class="flex items-start gap-3">

                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $iconBg }}"
                                    >

                                        <x-icon
                                            name="{{ $icon }}"
                                            class="h-5 w-5"
                                        />

                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <p
                                            class="truncate text-sm font-semibold text-secondary-dark"
                                            title="{{ $attachment->file_name }}"
                                        >
                                            {{ $attachment->file_name }}
                                        </p>


                                        <p class="mt-1 text-xs uppercase text-secondary">

                                            {{ strtoupper($extension ?: 'FILE') }}

                                            @if ($attachment->file_size)

                                                ·

                                                {{ number_format(
                                                    $attachment->file_size / 1024 / 1024,
                                                    2
                                                ) }}

                                                MB

                                            @endif

                                        </p>

                                    </div>

                                </div>


                                {{-- ================================================= --}}
                                {{-- ACTIONS --}}
                                {{-- ================================================= --}}

                                <div class="mt-5 flex gap-2">

                                    {{-- View --}}
                                    <a
                                        href="{{ $fileUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90"
                                    >

                                        <x-icon
                                            name="eye"
                                            class="h-4 w-4"
                                        />

                                        View

                                    </a>


                                    {{-- Download --}}
                                    <a
                                        href="{{ $fileUrl }}"
                                        download="{{ $attachment->file_name }}"
                                        class="inline-flex items-center justify-center rounded-lg border border-app-border bg-white px-4 py-2.5 text-sm font-medium text-secondary-dark transition hover:bg-surface-alt"
                                        title="Download"
                                    >

                                        <x-icon
                                            name="download"
                                            class="h-4 w-4"
                                        />

                                    </a>

                                </div>

                            </div>

                        </div>

                    @endforeach

                @endforeach

            </div>


            {{-- ================================================= --}}
            {{-- PAGINATION --}}
            {{-- ================================================= --}}

            @if ($manuals->hasPages())

                <div class="mt-6">

                    {{ $manuals->links() }}

                </div>

            @endif


        @else

            {{-- ================================================= --}}
            {{-- EMPTY STATE --}}
            {{-- ================================================= --}}

            <div
                class="rounded-xl border border-app-border bg-white px-6 py-16 text-center"
            >

                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-primary-light"
                >

                    <x-icon
                        name="document"
                        class="h-7 w-7 text-primary"
                    />

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