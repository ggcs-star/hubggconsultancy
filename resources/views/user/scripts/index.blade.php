<x-layout title="Scripts & Objection Handling" title-icon="book-open" subtitle="Pick a topic to watch a script in action and read the playbook">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="w-full sm:max-w-sm">
            <input type="hidden" name="language" value="{{ $language }}">
            @if ($type)
                <input type="hidden" name="type" value="{{ $type }}">
            @endif
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ $search }}" placeholder="Search topics..." class="form-input pl-10 {{ $search ? 'pr-9' : '' }}">
                @if ($search)
                    <a href="{{ route('user.scripts.index', ['language' => $language, 'type' => $type]) }}" title="Clear search" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <x-icon name="x" class="h-4 w-4" />
                    </a>
                @endif
            </div>
        </form>

        <div class="flex flex-wrap items-center justify-end gap-2">
            <a href="{{ route('user.scripts.index', ['language' => $language, 'search' => $search]) }}" class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ ! $type ? 'bg-brand-700 text-white shadow-sm' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                All
            </a>
            <a href="{{ route('user.scripts.index', ['language' => $language, 'search' => $search, 'type' => 'document']) }}" class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ $type === 'document' ? 'bg-brand-700 text-white shadow-sm' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                Documents
            </a>
            <a href="{{ route('user.scripts.index', ['language' => $language, 'search' => $search, 'type' => 'video']) }}" class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ $type === 'video' ? 'bg-brand-700 text-white shadow-sm' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                Videos
            </a>
        </div>
    </div>

    {{-- Language switch — only languages with at least one published script item get a tab. --}}
    @if (count($availableLanguages) >= 1)
        <div class="mt-4 flex items-center justify-end gap-2">
            @foreach ($availableLanguages as $value)
                <a href="{{ route('user.scripts.index', ['language' => $value, 'search' => $search, 'type' => $type]) }}" class="rounded-lg px-5 py-2 text-sm font-semibold transition {{ $language === $value ? 'bg-brand-700 text-white shadow-sm' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                    {{ ucfirst($value) }}
                </a>
            @endforeach
        </div>
    @endif

    @php
        // Uploaded documents render in the in-page reader below; external links open in a new tab.
        $scriptReaderFiles = [];
    @endphp

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($topics as $topic)
            @php
                $videos = $topic->items->where('type', 'video')->values();
                $documents = $topic->items->where('type', 'document')->values();
                $firstDocument = $documents->first();
                $firstVideo = $videos->first();

                foreach ($documents as $document) {
                    if (! $document->is_external) {
                        $scriptReaderFiles[$document->id] = [
                            'url' => $document->fileUrl(),
                            'name' => $document->title,
                            'extension' => strtolower(pathinfo($document->original_filename ?? $document->url, PATHINFO_EXTENSION)),
                        ];
                    }
                }
            @endphp

            <div class="card flex flex-col overflow-hidden border-l-4 border-l-brand-600" x-data="{ tab: '{{ $type === 'video' ? 'videos' : 'documents' }}' }">
                <div class="p-5 pb-0">
                    <p class="font-bold text-slate-800">{{ $topic->title }}</p>
                </div>

                <div class="mt-4 flex items-center gap-1 px-5">
                    @if (! $type || $type === 'document')
                        <button
                            type="button"
                            x-on:click="tab = 'documents'"
                            class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                            :class="tab === 'documents' ? 'bg-brand-50 text-brand-700' : 'text-slate-400 hover:text-slate-600'"
                        >
                            📄 Documents <span class="rounded-full bg-black/5 px-1.5 py-0.5 text-[10px]">{{ $documents->count() }}</span>
                        </button>
                    @endif
                    @if (! $type || $type === 'video')
                        <button
                            type="button"
                            x-on:click="tab = 'videos'"
                            class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                            :class="tab === 'videos' ? 'bg-brand-50 text-brand-700' : 'text-slate-400 hover:text-slate-600'"
                        >
                            🎬 Videos <span class="rounded-full bg-black/5 px-1.5 py-0.5 text-[10px]">{{ $videos->count() }}</span>
                        </button>
                    @endif
                </div>

                <div class="flex-1 px-5 py-4">
                    <div x-show="tab === 'documents'" x-cloak class="space-y-1">
                        @forelse ($documents as $document)
                            @if ($document->is_external)
                                <a href="{{ $document->previewUrl() }}" target="_blank" rel="noopener" class="flex items-center gap-2 rounded-lg px-2 py-2 text-sm text-slate-600 transition hover:bg-slate-50 hover:text-brand-700">
                                    @if ($document->thumbnailUrl())
                                        <img src="{{ $document->thumbnailUrl() }}" alt="" class="h-7 w-7 shrink-0 rounded-md object-cover">
                                    @else
                                        <x-icon name="document" class="h-4 w-4 shrink-0 text-slate-300" />
                                    @endif
                                    <span class="min-w-0 flex-1 truncate">{{ $document->title }}</span>
                                </a>
                            @else
                                <button type="button" onclick="window.openScriptReader({{ $document->id }})" class="flex w-full items-center gap-2 rounded-lg px-2 py-2 text-left text-sm text-slate-600 transition hover:bg-slate-50 hover:text-brand-700">
                                    @if ($document->thumbnailUrl())
                                        <img src="{{ $document->thumbnailUrl() }}" alt="" class="h-7 w-7 shrink-0 rounded-md object-cover">
                                    @else
                                        <x-icon name="document" class="h-4 w-4 shrink-0 text-slate-300" />
                                    @endif
                                    <span class="min-w-0 flex-1 truncate">{{ $document->title }}</span>
                                </button>
                            @endif
                        @empty
                            <p class="px-2 py-2 text-sm text-slate-400">No documents for this topic yet.</p>
                        @endforelse
                    </div>

                    <div x-show="tab === 'videos'" x-cloak class="space-y-1">
                        @forelse ($videos as $video)
                            <a href="{{ $video->previewUrl() }}" target="_blank" rel="noopener" class="flex items-center justify-between gap-2 rounded-lg px-2 py-2 text-sm text-slate-600 transition hover:bg-slate-50 hover:text-brand-700">
                                <span class="min-w-0 truncate">{{ $video->title }}</span>
                                <x-icon name="play-circle" class="h-4 w-4 shrink-0 text-slate-300" />
                            </a>
                        @empty
                            <p class="px-2 py-2 text-sm text-slate-400">No videos for this topic yet.</p>
                        @endforelse
                    </div>
                </div>

                @if ($firstDocument)
                    @if ($firstDocument->is_external)
                        <a href="{{ $firstDocument->previewUrl() }}" target="_blank" rel="noopener" x-show="tab === 'documents'" x-cloak class="flex items-center gap-1.5 border-t border-slate-100 px-5 py-4 text-sm font-semibold text-brand-700 hover:text-brand-800">
                            Open Document
                            <x-icon name="chevron-right" class="h-4 w-4" />
                        </a>
                    @else
                        <button type="button" onclick="window.openScriptReader({{ $firstDocument->id }})" x-show="tab === 'documents'" x-cloak class="flex w-full items-center gap-1.5 border-t border-slate-100 px-5 py-4 text-left text-sm font-semibold text-brand-700 hover:text-brand-800">
                            Open Document
                            <x-icon name="chevron-right" class="h-4 w-4" />
                        </button>
                    @endif
                @endif

                @if ($firstVideo)
                    <a href="{{ $firstVideo->previewUrl() }}" target="_blank" rel="noopener" x-show="tab === 'videos'" x-cloak class="flex items-center gap-1.5 border-t border-slate-100 px-5 py-4 text-sm font-semibold text-brand-700 hover:text-brand-800">
                        Start Learning
                        <x-icon name="chevron-right" class="h-4 w-4" />
                    </a>
                @endif
            </div>
        @empty
            <div class="card col-span-full p-10 text-center text-sm text-slate-400">
                No {{ ucfirst($language) }} scripts or objection-handling topics have been added yet.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $topics->links() }}
    </div>

    {{-- ========================================================= --}}
    {{-- IN-PAGE DOCUMENT READER --}}
    {{-- Same reader UI as Sales Manuals — uploaded documents preview --}}
    {{-- here instead of opening a new tab. --}}
    {{-- ========================================================= --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.8.0/mammoth.browser.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://unpkg.com/pptxviewjs@1.1.9/dist/PptxViewJS.min.js"></script>

    <script>
        window.scriptReaderFiles = @json($scriptReaderFiles);
    </script>

    <div
        id="scriptReader"
        class="fixed inset-0 z-[100] hidden bg-black/60 p-3 sm:p-6"
        role="dialog"
        aria-modal="true"
        aria-labelledby="scriptReaderTitle"
    >
        <div class="mx-auto flex h-full w-full max-w-6xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

            {{-- HEADER --}}
            <div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-4 py-3 sm:px-6">
                <div class="min-w-0 pr-4">
                    <h2 id="scriptReaderTitle" class="truncate text-base font-semibold text-slate-800">Document</h2>
                    <p id="scriptReaderMeta" class="text-xs text-slate-400">Read the document here without leaving this page.</p>
                </div>

                <button
                    type="button"
                    onclick="window.closeScriptReader()"
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50"
                    aria-label="Close document reader"
                >
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>

            {{-- BODY --}}
            <div id="scriptReaderBody" class="min-h-0 flex-1 overflow-auto bg-slate-100">
                <div id="scriptReaderLoading" class="flex min-h-full items-center justify-center p-8">
                    <div class="text-center">
                        <div class="mx-auto h-8 w-8 animate-spin rounded-full border-2 border-brand-600/20 border-t-brand-600"></div>
                        <p class="mt-3 text-sm text-slate-400">Opening file...</p>
                    </div>
                </div>

                {{-- PDF --}}
                <div id="scriptReaderPdfViewer" class="hidden min-h-full bg-slate-200 p-4 sm:p-6">
                    <div id="scriptReaderPdfPages" class="mx-auto flex w-fit flex-col items-center gap-5"></div>
                </div>

                {{-- DOCX --}}
                <div id="scriptReaderDocxViewer" class="hidden min-h-full p-4 sm:p-8">
                    <article id="scriptReaderDocxContent" class="mx-auto min-h-full max-w-4xl rounded-xl bg-white p-6 shadow-sm sm:p-10"></article>
                </div>

                {{-- XLS/XLSX/CSV --}}
                <div id="scriptReaderSheetViewer" class="hidden min-h-full p-3 sm:p-6">
                    <div id="scriptReaderSheetContent" class="mx-auto max-w-full overflow-auto rounded-xl border border-slate-200 bg-white shadow-sm"></div>
                </div>

                {{-- PPTX --}}
                <div id="scriptReaderPptxViewer" class="hidden min-h-full bg-slate-200 p-3 sm:p-6">
                    <div id="scriptReaderPptxContent" class="mx-auto min-h-[500px] w-full max-w-5xl overflow-auto rounded-xl bg-white shadow-sm"></div>
                </div>

                {{-- IMAGE --}}
                <div id="scriptReaderImageViewer" class="hidden min-h-full items-center justify-center p-4 sm:p-8">
                    <img id="scriptReaderImageContent" src="" alt="" class="max-h-full max-w-full rounded-xl bg-white object-contain shadow-xl">
                </div>

                {{-- TEXT --}}
                <div id="scriptReaderTextViewer" class="hidden min-h-full p-4 sm:p-8">
                    <pre id="scriptReaderTextContent" class="mx-auto max-w-5xl whitespace-pre-wrap break-words rounded-xl bg-white p-5 font-mono text-sm leading-6 text-slate-800 shadow-sm"></pre>
                </div>

                {{-- GENERIC BROWSER PREVIEW --}}
                <div id="scriptReaderGenericViewer" class="hidden min-h-full bg-white">
                    <iframe id="scriptReaderGenericFrame" src="about:blank" class="h-full min-h-[70vh] w-full border-0" title="File preview"></iframe>
                </div>

                {{-- UNSUPPORTED --}}
                <div id="scriptReaderUnsupportedViewer" class="hidden min-h-full items-center justify-center p-8">
                    <div class="max-w-md text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                            <x-icon name="file" class="h-7 w-7" />
                        </div>
                        <h3 class="mt-4 text-base font-semibold text-slate-800">Preview not available</h3>
                        <p id="scriptReaderUnsupportedText" class="mt-1 text-sm leading-6 text-slate-400">This file format cannot be rendered directly in the browser.</p>
                    </div>
                </div>

                <div id="scriptReaderError" class="hidden min-h-full items-center justify-center p-8">
                    <div class="max-w-md text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                            <x-icon name="alert-circle" class="h-7 w-7" />
                        </div>
                        <h3 class="mt-4 text-base font-semibold text-slate-800">Unable to preview this file</h3>
                        <p id="scriptReaderErrorText" class="mt-1 text-sm leading-6 text-slate-400">The file could not be opened.</p>
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="flex shrink-0 items-center justify-between border-t border-slate-100 bg-white px-4 py-3 sm:px-6">
                <div id="scriptReaderStatus" class="truncate pr-4 text-xs text-slate-400">Document preview</div>

                <div class="flex shrink-0 items-center gap-2">
                    <button type="button" id="scriptReaderDownload" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">
                        <x-icon name="download" class="h-4 w-4" />
                        Download
                    </button>

                    <button type="button" id="scriptReaderPrint" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">
                        <x-icon name="printer" class="h-4 w-4" />
                        Print
                    </button>

                    <button type="button" id="scriptReaderPrev" class="hidden inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40">
                        <x-icon name="chevron-left" class="h-4 w-4" />
                        Previous
                    </button>

                    <span id="scriptReaderPageIndicator" class="hidden rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700">1 / 1</span>

                    <button type="button" id="scriptReaderNext" class="hidden inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40">
                        Next
                        <x-icon name="chevron-right" class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.10.38/pdf.min.mjs';

        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.10.38/pdf.worker.min.mjs';

        (() => {
            const modal = document.getElementById('scriptReader');
            const loading = document.getElementById('scriptReaderLoading');
            const title = document.getElementById('scriptReaderTitle');
            const downloadButton = document.getElementById('scriptReaderDownload');
            const printButton = document.getElementById('scriptReaderPrint');

            const meta = document.getElementById('scriptReaderMeta');
            const status = document.getElementById('scriptReaderStatus');

            const pdfViewer = document.getElementById('scriptReaderPdfViewer');
            const pdfPages = document.getElementById('scriptReaderPdfPages');

            const docxViewer = document.getElementById('scriptReaderDocxViewer');
            const docxContent = document.getElementById('scriptReaderDocxContent');

            const sheetViewer = document.getElementById('scriptReaderSheetViewer');
            const sheetContent = document.getElementById('scriptReaderSheetContent');

            const pptxViewer = document.getElementById('scriptReaderPptxViewer');
            const pptxContent = document.getElementById('scriptReaderPptxContent');

            const imageViewer = document.getElementById('scriptReaderImageViewer');
            const imageContent = document.getElementById('scriptReaderImageContent');

            const textViewer = document.getElementById('scriptReaderTextViewer');
            const textContent = document.getElementById('scriptReaderTextContent');

            const genericViewer = document.getElementById('scriptReaderGenericViewer');
            const genericFrame = document.getElementById('scriptReaderGenericFrame');

            const unsupportedViewer = document.getElementById('scriptReaderUnsupportedViewer');
            const unsupportedText = document.getElementById('scriptReaderUnsupportedText');

            const errorViewer = document.getElementById('scriptReaderError');
            const errorText = document.getElementById('scriptReaderErrorText');

            const prev = document.getElementById('scriptReaderPrev');
            const next = document.getElementById('scriptReaderNext');
            const indicator = document.getElementById('scriptReaderPageIndicator');

            let pdf = null;
            let pdfPage = 1;
            let pptx = null;

            const allViewers = [
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

            const downloadFile = (file) => {
                if (!file) return;

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

                if (!pdfViewer.classList.contains('hidden')) {
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
                pdf = await pdfjsLib.getDocument({ url, withCredentials: false }).promise;

                show(pdfViewer);

                // Fit each page to the modal's width instead of a fixed scale, so wide
                // landscape/slide-style PDFs render fully without horizontal scrolling.
                const firstPage = await pdf.getPage(1);
                const referenceViewport = firstPage.getViewport({ scale: 1 });
                const availableWidth = pdfPages.clientWidth || pdfViewer.clientWidth || 800;
                const fitScale = Math.min(2.5, Math.max(0.4, (availableWidth - 32) / referenceViewport.width));

                for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
                    const page = pageNumber === 1 ? firstPage : await pdf.getPage(pageNumber);
                    const viewport = page.getViewport({ scale: fitScale });

                    const wrapper = document.createElement('div');
                    wrapper.dataset.page = pageNumber;
                    wrapper.className = 'overflow-hidden rounded-lg bg-white shadow-lg';

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
                        viewport: page.getViewport({ scale: fitScale * ratio }),
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

                const workbook = window.XLSX.read(buffer, { type: 'array', cellStyles: true });

                sheetContent.innerHTML = '';

                workbook.SheetNames.forEach((sheetName) => {
                    const section = document.createElement('section');
                    section.className = 'min-w-max';

                    const heading = document.createElement('div');
                    heading.className = 'sticky left-0 z-10 border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800';
                    heading.textContent = sheetName;

                    const table = window.XLSX.utils.sheet_to_html(workbook.Sheets[sheetName], { editable: false, header: '' });

                    const wrapper = document.createElement('div');
                    wrapper.className = 'overflow-auto p-4';
                    wrapper.innerHTML = table;

                    wrapper.querySelector('table')?.classList.add('min-w-full', 'border-collapse', 'text-sm');

                    wrapper.querySelectorAll('td, th').forEach((cell) => {
                        cell.classList.add('border', 'border-slate-200', 'px-3', 'py-2', 'text-left', 'align-top');
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

                pptx = new window.PptxViewJS.PPTXViewer({ canvas: document.createElement('canvas') });

                await pptx.loadFile(buffer);

                pptxContent.innerHTML = '';

                const canvas = document.createElement('canvas');
                canvas.className = 'mx-auto block max-w-full rounded-lg bg-white shadow-lg';

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
                unsupportedText.textContent = `.${extension || 'file'} files cannot be rendered directly in this browser viewer. The file remains attached to this topic.`;
                show(unsupportedViewer, true);
                status.textContent = 'Preview unavailable';
                finishLoading();
            };

            const openDocument = async (itemId) => {
                const file = window.scriptReaderFiles?.[itemId];

                if (!file || !modal) return;

                currentFile = file;
                title.textContent = file.name;
                meta.textContent = `${file.extension ? file.extension.toUpperCase() : 'FILE'} • Read-only preview`;

                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');

                resetViewer();

                try {
                    const extension = file.extension;

                    const imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'bmp', 'avif'];
                    const textExtensions = ['txt', 'csv', 'json', 'xml', 'md', 'log', 'html', 'htm', 'css', 'js', 'ts', 'php', 'py', 'java', 'sql'];

                    if (extension === 'pdf') {
                        await renderPdf(file.url);
                    } else if (extension === 'docx') {
                        await renderDocx(file.url);
                    } else if (['xlsx', 'xls', 'csv'].includes(extension)) {
                        await renderSheet(file.url, extension);
                    } else if (extension === 'pptx') {
                        await renderPptx(file.url);
                    } else if (imageExtensions.includes(extension)) {
                        renderImage(file.url, file.name);
                    } else if (textExtensions.includes(extension)) {
                        await renderText(file.url, extension);
                    } else {
                        renderGeneric(file.url);
                    }
                } catch (error) {
                    console.error('Script document preview error:', error);

                    finishLoading();
                    allViewers.forEach((element) => {
                        element.classList.add('hidden');
                        element.classList.remove('flex');
                    });

                    show(errorViewer, true);
                    errorText.textContent = 'This file could not be rendered in the browser. Please check the file format and storage URL.';
                    status.textContent = 'Preview error';
                }
            };

            const closeDocument = () => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');

                currentFile = null;
                resetViewer();
            };

            window.openScriptReader = openDocument;
            window.closeScriptReader = closeDocument;

            downloadButton.addEventListener('click', () => downloadFile(currentFile));
            printButton.addEventListener('click', printCurrent);

            prev.addEventListener('click', () => {
                if (!pdf || pdfPage <= 1) return;

                pdfPage--;

                const page = document.querySelector(`#scriptReaderPdfPages [data-page="${pdfPage}"]`);
                page?.scrollIntoView({ behavior: 'smooth', block: 'start' });

                setPageControls(pdfPage, pdf.numPages);
            });

            next.addEventListener('click', () => {
                if (!pdf || pdfPage >= pdf.numPages) return;

                pdfPage++;

                const page = document.querySelector(`#scriptReaderPdfPages [data-page="${pdfPage}"]`);
                page?.scrollIntoView({ behavior: 'smooth', block: 'start' });

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

</x-layout>
