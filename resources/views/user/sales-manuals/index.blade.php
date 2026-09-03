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

                    @php
                        $manualTypeLabel = match ($manual->type) {
                            'manual' => 'Manual',
                            'guide' => 'Sales Guide',
                            'cheat_sheet' => 'Cheat Sheet',
                            'faq' => 'FAQ',
                            'sop' => 'SOP',
                            'script' => 'Sales Script',
                            default => ucfirst(str_replace('_', ' ', $manual->type ?? 'Resource')),
                        };

                        $manualIcon = match ($manual->type) {
                            'faq' => 'help-circle',
                            'cheat_sheet' => 'list',
                            'sop' => 'check-circle',
                            'script' => 'edit',
                            'guide', 'manual' => 'file-text',
                            default => 'document',
                        };

                        $manualIconBg = match ($manual->type) {
                            'faq' => 'bg-purple-50 text-purple-600',
                            'cheat_sheet' => 'bg-blue-50 text-blue-600',
                            'sop' => 'bg-green-50 text-green-600',
                            'script' => 'bg-orange-50 text-orange-600',
                            default => 'bg-slate-50 text-slate-600',
                        };

                        $manualHasContent = filled($manual->content) || filled($manual->description);
                        $manualHasAttachments = $manual->attachments->count() > 0;
                    @endphp

                    {{-- ================================================= --}}
                    {{-- CONTENT RESOURCE --}}
                    {{-- Every manually-created resource is shown, even when it has 0 files. --}}
                    {{-- ================================================= --}}

                    @if ($manualHasContent || !$manualHasAttachments)
                        <div
                            class="overflow-hidden rounded-xl border border-app-border bg-white transition hover:-translate-y-0.5 hover:shadow-md"
                        >
                            @if (!empty($manual->cover_image))
                                <div class="flex h-56 w-full items-center justify-center overflow-hidden bg-slate-50">
                                    <img
                                        src="{{ asset('storage/' . ltrim($manual->cover_image, '/')) }}"
                                        alt="{{ $manual->title }}"
                                        class="h-full w-full object-contain p-2"
                                        loading="lazy"
                                    >
                                </div>
                            @else
                                <div class="flex h-56 w-full items-center justify-center bg-surface-alt">
                                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl {{ $manualIconBg }}">
                                        <x-icon name="{{ $manualIcon }}" class="h-10 w-10" />
                                    </div>
                                </div>
                            @endif

                            <div class="p-5">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $manualIconBg }}">
                                        <x-icon name="{{ $manualIcon }}" class="h-5 w-5" />
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p
                                            class="truncate text-sm font-semibold text-secondary-dark"
                                            title="{{ $manual->title }}"
                                        >
                                            {{ $manual->title }}
                                        </p>

                                        <p class="mt-1 text-xs uppercase text-secondary">
                                            {{ $manualTypeLabel }}
                                            @if ($manual->category)
                                                · {{ $manual->category }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                @if ($manual->description)
                                    <p class="mt-4 text-sm leading-6 text-secondary">
                                        {{ $manual->description }}
                                    </p>
                                @endif

                                @if ($manual->content)
                                    <div class="mt-4 max-h-32 overflow-hidden rounded-lg border border-app-border bg-surface-alt p-3">
                                        <p class="whitespace-pre-wrap text-sm leading-6 text-secondary-dark">{{ $manual->content }}</p>
                                    </div>
                                @endif

                                <div class="mt-5 grid grid-cols-2 gap-2">
                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-3 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90"
                                        onclick="window.openSalesManualReader('content-{{ $manual->id }}')"
                                    >
                                        <x-icon name="eye" class="h-4 w-4" />
                                        View
                                    </button>

                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-app-border bg-white px-3 py-2.5 text-sm font-medium text-secondary-dark transition hover:bg-surface-alt"
                                        onclick="window.downloadSalesManual('content-{{ $manual->id }}')"
                                    >
                                        <x-icon name="download" class="h-4 w-4" />
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

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

                            <div class="block">

                                <div class="flex h-56 w-full items-center justify-center overflow-hidden bg-slate-50">

                                    <img
                                        src="{{ $coverUrl }}"
                                        alt="{{ $manual->title }}"
                                        class="h-full w-full object-contain p-2"
                                        loading="lazy"
                                    >

                                </div>

                            </div>


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

                                <div class="block">

                                    <div class="flex h-56 w-full items-center justify-center overflow-hidden bg-slate-50">

                                        <img
                                            src="{{ $fileUrl }}"
                                            alt="{{ $attachment->file_name }}"
                                            class="h-full w-full object-contain p-2"
                                            loading="lazy"
                                        >

                                    </div>

                                </div>


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
                                {{-- DOCUMENT READER --}}
                                {{-- ================================================= --}}

                                <div class="mt-5 grid grid-cols-2 gap-2">
                                    <button
                                        type="button"
                                        data-manual-id="{{ $attachment->id }}"
                                        data-manual-name="{{ $attachment->file_name }}"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-3 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90"
                                        onclick="window.openSalesManualReader('{{ $attachment->id }}')"
                                    >
                                        <x-icon name="eye" class="h-4 w-4" />
                                        View
                                    </button>

                                    <a
                                        href="{{ $fileUrl }}"
                                        download="{{ $attachment->file_name }}"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-app-border bg-white px-3 py-2.5 text-sm font-medium text-secondary-dark transition hover:bg-surface-alt"
                                    >
                                        <x-icon name="download" class="h-4 w-4" />
                                        Save
                                    </a>
                                </div>

                            </div>

                        </div>

                    @endforeach

                @endforeach

            </div>




    {{-- ========================================================= --}}
    {{-- UNIVERSAL SALES MANUAL READER --}}
    {{-- Same existing UI; viewer changes by file type only. --}}
    {{-- ========================================================= --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.8.0/mammoth.browser.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://unpkg.com/pptxviewjs@1.1.9/dist/PptxViewJS.min.js"></script>

    <script>
        window.salesManualFiles = {
@foreach ($manuals as $manual)
            'content-{{ $manual->id }}': {
                kind: 'content',
                id: @json($manual->id),
                name: @json($manual->title),
                type: @json($manual->type),
                extension: 'content',
                description: @json($manual->description),
                content: @json($manual->content),
            },
@foreach ($manual->attachments as $attachment)
            '{{ $attachment->id }}': {
                kind: 'file',
                url: @json(asset('storage/' . ltrim($attachment->file_path, '/'))),
                name: @json($attachment->file_name),
                extension: @json(strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION))),
            },
@endforeach
@endforeach
        };
    </script>

    <div
        id="salesManualReader"
        class="fixed inset-0 z-[100] hidden bg-black/60 p-3 sm:p-6"
        role="dialog"
        aria-modal="true"
        aria-labelledby="salesManualReaderTitle"
    >
        <div class="mx-auto flex h-full w-full max-w-6xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

            {{-- HEADER --}}
            <div class="flex shrink-0 items-center justify-between border-b border-app-border px-4 py-3 sm:px-6">
                <div class="min-w-0 pr-4">
                    <h2
                        id="salesManualReaderTitle"
                        class="truncate text-base font-semibold text-secondary-dark"
                    >
                        Sales Manual
                    </h2>

                    <p
                        id="salesManualReaderMeta"
                        class="text-xs text-secondary"
                    >
                        Read the document here without leaving this page.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="window.closeSalesManualReader()"
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-app-border bg-white text-secondary-dark transition hover:bg-surface-alt"
                    aria-label="Close document reader"
                >
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>

            {{-- BODY --}}
            <div
                id="salesManualReaderBody"
                class="min-h-0 flex-1 overflow-auto bg-slate-100"
            >
                <div
                    id="salesManualReaderLoading"
                    class="flex min-h-full items-center justify-center p-8"
                >
                    <div class="text-center">
                        <div class="mx-auto h-8 w-8 animate-spin rounded-full border-2 border-primary/20 border-t-primary"></div>
                        <p class="mt-3 text-sm text-secondary">
                            Opening file...
                        </p>
                    </div>
                </div>

                {{-- MANUAL / FAQ / SOP / SCRIPT CONTENT --}}
                <div
                    id="salesManualContentViewer"
                    class="hidden min-h-full p-4 sm:p-8"
                >
                    <article
                        id="salesManualContentBody"
                        class="mx-auto max-w-4xl rounded-xl bg-white p-6 shadow-sm sm:p-10"
                    ></article>
                </div>

                {{-- PDF --}}
                <div
                    id="salesManualPdfViewer"
                    class="hidden min-h-full bg-slate-200 p-4 sm:p-6"
                >
                    <div
                        id="salesManualPdfPages"
                        class="mx-auto flex w-fit flex-col items-center gap-5"
                    ></div>
                </div>

                {{-- DOCX --}}
                <div
                    id="salesManualDocxViewer"
                    class="hidden min-h-full p-4 sm:p-8"
                >
                    <article
                        id="salesManualDocxContent"
                        class="mx-auto min-h-full max-w-4xl rounded-xl bg-white p-6 shadow-sm sm:p-10"
                    ></article>
                </div>

                {{-- XLS/XLSX/CSV --}}
                <div
                    id="salesManualSheetViewer"
                    class="hidden min-h-full p-3 sm:p-6"
                >
                    <div
                        id="salesManualSheetContent"
                        class="mx-auto max-w-full overflow-auto rounded-xl border border-app-border bg-white shadow-sm"
                    ></div>
                </div>

                {{-- PPTX --}}
                <div
                    id="salesManualPptxViewer"
                    class="hidden min-h-full bg-slate-200 p-3 sm:p-6"
                >
                    <div
                        id="salesManualPptxContent"
                        class="mx-auto min-h-[500px] w-full max-w-5xl overflow-auto rounded-xl bg-white shadow-sm"
                    ></div>
                </div>

                {{-- IMAGE --}}
                <div
                    id="salesManualImageViewer"
                    class="hidden min-h-full items-center justify-center p-4 sm:p-8"
                >
                    <img
                        id="salesManualImageContent"
                        src=""
                        alt=""
                        class="max-h-full max-w-full rounded-xl bg-white object-contain shadow-xl"
                    >
                </div>

                {{-- TEXT --}}
                <div
                    id="salesManualTextViewer"
                    class="hidden min-h-full p-4 sm:p-8"
                >
                    <pre
                        id="salesManualTextContent"
                        class="mx-auto max-w-5xl whitespace-pre-wrap break-words rounded-xl bg-white p-5 font-mono text-sm leading-6 text-secondary-dark shadow-sm"
                    ></pre>
                </div>

                {{-- GENERIC BROWSER PREVIEW --}}
                <div
                    id="salesManualGenericViewer"
                    class="hidden min-h-full bg-white"
                >
                    <iframe
                        id="salesManualGenericFrame"
                        src="about:blank"
                        class="h-full min-h-[70vh] w-full border-0"
                        title="File preview"
                    ></iframe>
                </div>

                {{-- UNSUPPORTED --}}
                <div
                    id="salesManualUnsupportedViewer"
                    class="hidden min-h-full items-center justify-center p-8"
                >
                    <div class="max-w-md text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                            <x-icon name="file" class="h-7 w-7" />
                        </div>

                        <h3 class="mt-4 text-base font-semibold text-secondary-dark">
                            Preview not available
                        </h3>

                        <p
                            id="salesManualUnsupportedText"
                            class="mt-1 text-sm leading-6 text-secondary"
                        >
                            This file format cannot be rendered directly in the browser.
                        </p>
                    </div>
                </div>

                <div
                    id="salesManualReaderError"
                    class="hidden min-h-full items-center justify-center p-8"
                >
                    <div class="max-w-md text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                            <x-icon name="alert-circle" class="h-7 w-7" />
                        </div>

                        <h3 class="mt-4 text-base font-semibold text-secondary-dark">
                            Unable to preview this file
                        </h3>

                        <p
                            id="salesManualReaderErrorText"
                            class="mt-1 text-sm leading-6 text-secondary"
                        >
                            The file could not be opened.
                        </p>
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="flex shrink-0 items-center justify-between border-t border-app-border bg-white px-4 py-3 sm:px-6">
                <div
                    id="salesManualReaderStatus"
                    class="truncate pr-4 text-xs text-secondary"
                >
                    Document preview
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    <button
                        type="button"
                        id="salesManualDownload"
                        class="inline-flex items-center gap-1 rounded-lg border border-app-border px-3 py-2 text-xs font-medium text-secondary-dark transition hover:bg-surface-alt"
                    >
                        <x-icon name="download" class="h-4 w-4" />
                        Download
                    </button>

                    <button
                        type="button"
                        id="salesManualPrint"
                        class="inline-flex items-center gap-1 rounded-lg border border-app-border px-3 py-2 text-xs font-medium text-secondary-dark transition hover:bg-surface-alt"
                    >
                        <x-icon name="printer" class="h-4 w-4" />
                        Print
                    </button>

                    <button
                        type="button"
                        id="salesManualPrev"
                        class="hidden inline-flex items-center gap-1 rounded-lg border border-app-border px-3 py-2 text-xs font-medium text-secondary-dark transition hover:bg-surface-alt disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        <x-icon name="chevron-left" class="h-4 w-4" />
                        Previous
                    </button>

                    <span
                        id="salesManualPageIndicator"
                        class="hidden rounded-lg bg-surface-alt px-3 py-2 text-xs font-semibold text-secondary-dark"
                    >
                        1 / 1
                    </span>

                    <button
                        type="button"
                        id="salesManualNext"
                        class="hidden inline-flex items-center gap-1 rounded-lg border border-app-border px-3 py-2 text-xs font-medium text-secondary-dark transition hover:bg-surface-alt disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        Next
                        <x-icon name="chevron-right" class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.10.38/pdf.min.mjs';

        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.10.38/pdf.worker.min.mjs';

        (() => {
            const modal = document.getElementById('salesManualReader');
            const body = document.getElementById('salesManualReaderBody');
            const loading = document.getElementById('salesManualReaderLoading');
            const title = document.getElementById('salesManualReaderTitle');
            const downloadButton = document.getElementById('salesManualDownload');
            const printButton = document.getElementById('salesManualPrint');

            const contentViewer = document.getElementById('salesManualContentViewer');
            const contentBody = document.getElementById('salesManualContentBody');
            const meta = document.getElementById('salesManualReaderMeta');
            const status = document.getElementById('salesManualReaderStatus');

            const pdfViewer = document.getElementById('salesManualPdfViewer');
            const pdfPages = document.getElementById('salesManualPdfPages');

            const docxViewer = document.getElementById('salesManualDocxViewer');
            const docxContent = document.getElementById('salesManualDocxContent');

            const sheetViewer = document.getElementById('salesManualSheetViewer');
            const sheetContent = document.getElementById('salesManualSheetContent');

            const pptxViewer = document.getElementById('salesManualPptxViewer');
            const pptxContent = document.getElementById('salesManualPptxContent');

            const imageViewer = document.getElementById('salesManualImageViewer');
            const imageContent = document.getElementById('salesManualImageContent');

            const textViewer = document.getElementById('salesManualTextViewer');
            const textContent = document.getElementById('salesManualTextContent');

            const genericViewer = document.getElementById('salesManualGenericViewer');
            const genericFrame = document.getElementById('salesManualGenericFrame');

            const unsupportedViewer = document.getElementById('salesManualUnsupportedViewer');
            const unsupportedText = document.getElementById('salesManualUnsupportedText');

            const errorViewer = document.getElementById('salesManualReaderError');
            const errorText = document.getElementById('salesManualReaderErrorText');

            const prev = document.getElementById('salesManualPrev');
            const next = document.getElementById('salesManualNext');
            const indicator = document.getElementById('salesManualPageIndicator');

            let pdf = null;
            let pdfPage = 1;
            let pptx = null;

            const allViewers = [
                contentViewer,
                pdfViewer,
                docxViewer,
                sheetViewer,
                pptxViewer,
                imageViewer,
                textViewer,
                genericViewer,
                unsupportedViewer,
                errorViewer,
            ];

            const resetViewer = () => {
                allViewers.forEach((element) => {
                    element.classList.add('hidden');
                    element.classList.remove('flex');
                });

                loading.classList.remove('hidden');

                contentBody.innerHTML = '';
                pdfPages.innerHTML = '';
                docxContent.innerHTML = '';
                sheetContent.innerHTML = '';
                pptxContent.innerHTML = '';
                textContent.textContent = '';
                imageContent.removeAttribute('src');
                genericFrame.src = 'about:blank';

                pdf = null;
                pptx = null;
                prev.classList.add('hidden');
                next.classList.add('hidden');
                indicator.classList.add('hidden');
            };

            const show = (element, flex = false) => {
                element.classList.remove('hidden');

                if (flex) {
                    element.classList.add('flex');
                }
            };

            const finishLoading = () => {
                loading.classList.add('hidden');
            };

            const setPageControls = (current, total, enabled = true) => {
                if (!enabled || total <= 1) {
                    prev.classList.add('hidden');
                    next.classList.add('hidden');
                    indicator.classList.add('hidden');
                    return;
                }

                prev.classList.remove('hidden');
                next.classList.remove('hidden');
                indicator.classList.remove('hidden');

                indicator.textContent = `${current} / ${total}`;
                prev.disabled = current <= 1;
                next.disabled = current >= total;
            };

            let currentFile = null;

            const renderContent = (file) => {
                const heading = document.createElement('h1');
                heading.className = 'text-2xl font-semibold text-secondary-dark';
                heading.textContent = file.name;

                const type = document.createElement('p');
                type.className = 'mt-1 text-xs font-semibold uppercase tracking-wide text-primary';
                type.textContent = (file.type || 'resource').replaceAll('_', ' ');

                contentBody.innerHTML = '';
                contentBody.appendChild(heading);
                contentBody.appendChild(type);

                if (file.description) {
                    const description = document.createElement('p');
                    description.className = 'mt-5 text-sm leading-6 text-secondary';
                    description.textContent = file.description;
                    contentBody.appendChild(description);
                }

                const content = document.createElement('div');
                content.className = 'mt-6 whitespace-pre-wrap text-[15px] leading-7 text-secondary-dark';
                content.textContent = file.content || 'No additional content was entered for this resource.';
                contentBody.appendChild(content);

                show(contentViewer);
                status.textContent = `${(file.type || 'Resource').replaceAll('_', ' ')} content`;
                finishLoading();
            };

            const downloadFile = (file) => {
                if (!file) return;

                if (file.kind === 'content') {
                    const html = `<!doctype html><html><head><meta charset="utf-8"><title>${escapeHtml(file.name)}</title></head><body><h1>${escapeHtml(file.name)}</h1><p>${escapeHtml(file.description || '')}</p><div style="white-space:pre-wrap;line-height:1.6">${escapeHtml(file.content || '')}</div></body></html>`;
                    const blob = new Blob([html], { type: 'text/html;charset=utf-8' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = `${file.name || 'sales-resource'}.html`;
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    URL.revokeObjectURL(url);
                    return;
                }

                const link = document.createElement('a');
                link.href = file.url;
                link.download = file.name || 'download';
                link.target = '_self';
                document.body.appendChild(link);
                link.click();
                link.remove();
            };

            const escapeHtml = (value) => {
                const div = document.createElement('div');
                div.textContent = value ?? '';
                return div.innerHTML;
            };

            const printHtml = (html) => {
                const printWindow = window.open('', '_blank', 'width=1000,height=800');
                if (!printWindow) return;

                printWindow.document.open();
                printWindow.document.write(`<!doctype html><html><head><title>Print</title><style>body{font-family:Arial,sans-serif;padding:32px;color:#1f2937}img{max-width:100%}table{border-collapse:collapse;width:100%}td,th{border:1px solid #ddd;padding:8px;text-align:left}@media print{body{padding:0}}</style></head><body>${html}</body></html>`);
                printWindow.document.close();
                printWindow.focus();
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 300);
            };

            const printCurrent = () => {
                if (!currentFile) return;

                if (currentFile.kind === 'content') {
                    printHtml(contentBody.innerHTML);
                    return;
                }

                if (pdfViewer.classList.contains('hidden') === false) {
                    const images = [...pdfPages.querySelectorAll('canvas')].map(canvas => `<img src="${canvas.toDataURL('image/png')}">`).join('<div style="page-break-after:always"></div>');
                    printHtml(images);
                    return;
                }

                if (!docxViewer.classList.contains('hidden')) {
                    printHtml(docxContent.innerHTML);
                    return;
                }

                if (!sheetViewer.classList.contains('hidden')) {
                    printHtml(sheetContent.innerHTML);
                    return;
                }

                if (!pptxViewer.classList.contains('hidden')) {
                    const canvas = pptxContent.querySelector('canvas');
                    printHtml(canvas ? `<img src="${canvas.toDataURL('image/png')}">` : pptxContent.innerHTML);
                    return;
                }

                if (!imageViewer.classList.contains('hidden')) {
                    printHtml(`<img src="${escapeHtml(imageContent.src)}">`);
                    return;
                }

                if (!textViewer.classList.contains('hidden')) {
                    printHtml(`<pre>${escapeHtml(textContent.textContent)}</pre>`);
                    return;
                }

                try {
                    genericFrame.contentWindow?.print();
                } catch (e) {
                    window.print();
                }
            };

            const renderPdf = async (url) => {
                pdf = await pdfjsLib.getDocument({
                    url,
                    withCredentials: false,
                }).promise;

                show(pdfViewer);

                for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
                    const page = await pdf.getPage(pageNumber);
                    const viewport = page.getViewport({ scale: 1.15 });

                    const wrapper = document.createElement('div');
                    wrapper.dataset.page = pageNumber;
                    wrapper.className =
                        'overflow-hidden rounded-lg bg-white shadow-lg';

                    const canvas = document.createElement('canvas');
                    const ratio = Math.min(window.devicePixelRatio || 1, 2);

                    canvas.width = Math.floor(viewport.width * ratio);
                    canvas.height = Math.floor(viewport.height * ratio);
                    canvas.style.width = `${viewport.width}px`;
                    canvas.style.height = `${viewport.height}px`;
                    canvas.className = 'block';

                    wrapper.appendChild(canvas);
                    pdfPages.appendChild(wrapper);

                    await page.render({
                        canvasContext: canvas.getContext('2d', { alpha: false }),
                        viewport: page.getViewport({ scale: 1.15 * ratio }),
                    }).promise;
                }

                pdfPage = 1;
                setPageControls(1, pdf.numPages);
                status.textContent = `${pdf.numPages} page${pdf.numPages === 1 ? '' : 's'}`;
                finishLoading();
            };

            const renderDocx = async (url) => {
                const response = await fetch(url);
                if (!response.ok) throw new Error('Unable to fetch DOCX');

                const buffer = await response.arrayBuffer();

                const result = await window.mammoth.convertToHtml(
                    { arrayBuffer: buffer },
                    {
                        styleMap: [
                            "p[style-name='Title'] => h1:fresh",
                            "p[style-name='Heading 1'] => h2:fresh",
                            "p[style-name='Heading 2'] => h3:fresh",
                        ],
                    }
                );

                docxContent.innerHTML = result.value;
                show(docxViewer);
                status.textContent = 'Word document';
                finishLoading();
            };

            const renderSheet = async (url, extension) => {
                const response = await fetch(url);
                if (!response.ok) throw new Error('Unable to fetch spreadsheet');

                const buffer = await response.arrayBuffer();

                const workbook = window.XLSX.read(buffer, {
                    type: 'array',
                    cellStyles: true,
                });

                sheetContent.innerHTML = '';

                workbook.SheetNames.forEach((sheetName) => {
                    const section = document.createElement('section');
                    section.className = 'min-w-max';

                    const heading = document.createElement('div');
                    heading.className =
                        'sticky left-0 z-10 border-b border-app-border bg-slate-50 px-4 py-3 text-sm font-semibold text-secondary-dark';
                    heading.textContent = sheetName;

                    const table = window.XLSX.utils.sheet_to_html(
                        workbook.Sheets[sheetName],
                        {
                            editable: false,
                            header: '',
                        }
                    );

                    const wrapper = document.createElement('div');
                    wrapper.className = 'overflow-auto p-4';
                    wrapper.innerHTML = table;

                    wrapper.querySelector('table')?.classList.add(
                        'min-w-full',
                        'border-collapse',
                        'text-sm'
                    );

                    wrapper.querySelectorAll('td, th').forEach((cell) => {
                        cell.classList.add(
                            'border',
                            'border-app-border',
                            'px-3',
                            'py-2',
                            'text-left',
                            'align-top'
                        );
                    });

                    section.appendChild(heading);
                    section.appendChild(wrapper);
                    sheetContent.appendChild(section);
                });

                show(sheetViewer);
                status.textContent = `${extension.toUpperCase()} spreadsheet`;
                finishLoading();
            };

            const renderPptx = async (url) => {
                const response = await fetch(url);
                if (!response.ok) throw new Error('Unable to fetch PPTX');

                const buffer = await response.arrayBuffer();

                if (!window.PptxViewJS?.PPTXViewer) {
                    throw new Error('PPTX viewer library is unavailable');
                }

                show(pptxViewer);

                pptx = new window.PptxViewJS.PPTXViewer({
                    canvas: document.createElement('canvas'),
                });

                /*
                 * Use the library's browser renderer when available.
                 * The viewer accepts an ArrayBuffer and renders client-side.
                 */
                await pptx.loadFile(buffer);

                pptxContent.innerHTML = '';

                const canvas = document.createElement('canvas');
                canvas.className =
                    'mx-auto block max-w-full rounded-lg bg-white shadow-lg';

                pptxContent.appendChild(canvas);

                pptx.canvas = canvas;
                await pptx.render();

                status.textContent = 'PowerPoint presentation';
                finishLoading();
            };

            const renderImage = (url, fileName) => {
                imageContent.src = url;
                imageContent.alt = fileName;
                show(imageViewer, true);
                status.textContent = 'Image';
                finishLoading();
            };

            const renderText = async (url, extension) => {
                const response = await fetch(url);
                if (!response.ok) throw new Error('Unable to fetch text file');

                textContent.textContent = await response.text();
                show(textViewer);
                status.textContent = `${extension.toUpperCase()} text file`;
                finishLoading();
            };

            const renderGeneric = (url) => {
                genericFrame.src = url;
                show(genericViewer);
                status.textContent = 'Browser preview';
                finishLoading();
            };

            const renderUnsupported = (extension) => {
                unsupportedText.textContent =
                    `.${extension || 'file'} files cannot be rendered directly in this browser viewer. The file remains attached to the sales manual.`;
                show(unsupportedViewer, true);
                status.textContent = 'Preview unavailable';
                finishLoading();
            };

            const openDocument = async (attachmentId) => {
                const file = window.salesManualFiles?.[attachmentId];

                if (!file || !modal) return;

                currentFile = file;
                title.textContent = file.name;
                meta.textContent =
                    `${file.extension ? file.extension.toUpperCase() : 'FILE'} • Read-only preview`;

                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');

                resetViewer();

                try {
                    const extension = file.extension;

                    if (file.kind === 'content') {
                        renderContent(file);
                        return;
                    }

                    const imageExtensions = [
                        'jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'bmp', 'avif'
                    ];

                    const textExtensions = [
                        'txt', 'csv', 'json', 'xml', 'md', 'log', 'html', 'htm',
                        'css', 'js', 'ts', 'php', 'py', 'java', 'sql'
                    ];

                    if (extension === 'pdf') {
                        await renderPdf(file.url);
                    } else if (extension === 'docx') {
                        await renderDocx(file.url);
                    } else if (['xlsx', 'xls', 'csv'].includes(extension)) {
                        if (extension === 'csv') {
                            await renderSheet(file.url, extension);
                        } else {
                            await renderSheet(file.url, extension);
                        }
                    } else if (extension === 'pptx') {
                        await renderPptx(file.url);
                    } else if (imageExtensions.includes(extension)) {
                        renderImage(file.url, file.name);
                    } else if (textExtensions.includes(extension)) {
                        await renderText(file.url, extension);
                    } else if ([
                        'mp4', 'webm', 'ogg', 'mp3', 'wav', 'm4a'
                    ].includes(extension)) {
                        renderGeneric(file.url);
                    } else {
                        renderGeneric(file.url);
                    }
                } catch (error) {
                    console.error('Sales manual preview error:', error);

                    finishLoading();
                    allViewers.forEach((element) => {
                        element.classList.add('hidden');
                        element.classList.remove('flex');
                    });

                    show(errorViewer, true);
                    errorText.textContent =
                        'This file could not be rendered in the browser. Please check the file format and storage URL.';
                    status.textContent = 'Preview error';
                }
            };

            const closeDocument = () => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');

                currentFile = null;
                resetViewer();
            };

            window.downloadSalesManual = (id) => {
                downloadFile(window.salesManualFiles?.[id]);
            };

            window.openSalesManualReader = openDocument;
            window.closeSalesManualReader = closeDocument;

            downloadButton.addEventListener('click', () => downloadFile(currentFile));
            printButton.addEventListener('click', printCurrent);

            prev.addEventListener('click', () => {
                if (!pdf || pdfPage <= 1) return;

                pdfPage--;

                const page = document.querySelector(
                    `[data-page="${pdfPage}"]`
                );

                page?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });

                setPageControls(pdfPage, pdf.numPages);
            });

            next.addEventListener('click', () => {
                if (!pdf || pdfPage >= pdf.numPages) return;

                pdfPage++;

                const page = document.querySelector(
                    `[data-page="${pdfPage}"]`
                );

                page?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });

                setPageControls(pdfPage, pdf.numPages);
            });

            document.addEventListener('keydown', (event) => {
                if (modal.classList.contains('hidden')) return;

                if (event.key === 'Escape') {
                    closeDocument();
                }

                if (event.key === 'ArrowLeft' && pdf) {
                    prev.click();
                }

                if (event.key === 'ArrowRight' && pdf) {
                    next.click();
                }
            });

            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeDocument();
                }
            });
        })();
    </script>

            {{-- ================================================= --}}
            {{-- PAGINATION --}}
            {{-- ================================================= --}}

            <div class="mt-6">

                {{ $manuals->links() }}

            </div>


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