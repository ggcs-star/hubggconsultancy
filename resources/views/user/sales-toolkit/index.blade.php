<x-layout title="Sales Toolkit" title-icon="briefcase" subtitle="Scripts, decks and templates — click any card to open it">

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <input type="hidden" name="language" value="{{ $language }}">
            <div class="relative w-full sm:max-w-sm">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search toolkit items..." class="form-input pl-10">
            </div>

            <select name="category" class="form-input w-full sm:w-56" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn-primary shrink-0 sm:w-auto">
                <x-icon name="search" class="h-4 w-4" />
                Search
            </button>

            @if (request('search') || request('category'))
                <a href="{{ route('user.sales-toolkit.index', ['language' => $language]) }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Reset</a>
            @endif
        </form>

        {{-- Language switch — only languages with at least one toolkit item get a tab. --}}
        @if (count($availableLanguages) >= 1)
            <div class="flex items-center gap-2">
                @foreach ($availableLanguages as $value)
                    <a href="{{ route('user.sales-toolkit.index', ['language' => $value, 'search' => request('search'), 'category' => request('category')]) }}" class="rounded-lg px-5 py-2 text-sm font-semibold transition {{ $language === $value ? 'bg-brand-700 text-white shadow-sm' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                        {{ ucfirst($value) }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    @php
        $toolkitReaderFiles = [];

        foreach ($items as $item) {
            $toolkitReaderFiles[$item->id] = [
                'url' => $item->fileUrl(),
                'name' => $item->title,
                'extension' => strtolower(pathinfo($item->original_filename ?? $item->url, PATHINFO_EXTENSION)),
            ];
        }
    @endphp

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($items as $item)
            <button type="button" onclick="window.openToolkitReader({{ $item->id }})"
                class="card flex flex-col overflow-hidden border-l-4 border-l-brand-600 text-left transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="relative h-40 w-full">
                    <div class="absolute inset-0 flex items-center justify-center bg-brand-50 text-brand-600">
                        <x-icon name="briefcase" class="h-10 w-10" />
                    </div>
                    @if ($item->thumbnailUrl())
                        <img src="{{ $item->thumbnailUrl() }}" alt="" class="absolute inset-0 h-40 w-full object-cover" onerror="this.remove()">
                    @endif
                </div>

                <div class="flex flex-1 flex-col p-5">
                    <div class="flex items-start justify-between gap-2">
                        <p class="min-w-0 truncate font-bold text-slate-800">{{ $item->title }}</p>
                        @if ($item->category)
                            <span class="badge badge-slate shrink-0">{{ $item->category }}</span>
                        @endif
                    </div>
                    @if ($item->description)
                        <p class="mt-2 flex-1 text-sm text-slate-500">{{ $item->description }}</p>
                    @endif
                </div>
            </button>
        @empty
            <div class="card col-span-full p-10 text-center text-sm text-slate-400">
                @if (request('search') || request('category'))
                    No toolkit items match your search or filter.
                @else
                    No {{ ucfirst($language) }} toolkit items have been added yet.
                @endif
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $items->links() }}
    </div>

    {{-- ========================================================= --}}
    {{-- IN-PAGE DOCUMENT READER --}}
    {{-- Same reader UI as Sales Manuals / Scripts & Objection --}}
    {{-- Handling — toolkit files preview here instead of opening --}}
    {{-- a new tab. --}}
    {{-- ========================================================= --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.8.0/mammoth.browser.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://unpkg.com/pptxviewjs@1.1.9/dist/PptxViewJS.min.js"></script>

    <script>
        window.toolkitReaderFiles = @json($toolkitReaderFiles);
    </script>

    <div
        id="toolkitReader"
        class="fixed inset-0 z-[100] hidden bg-black/60 p-3 sm:p-6"
        role="dialog"
        aria-modal="true"
        aria-labelledby="toolkitReaderTitle"
    >
        <div class="mx-auto flex h-full w-full max-w-6xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

            {{-- HEADER --}}
            <div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-4 py-3 sm:px-6">
                <div class="min-w-0 pr-4">
                    <h2 id="toolkitReaderTitle" class="truncate text-base font-semibold text-slate-800">Document</h2>
                    <p id="toolkitReaderMeta" class="text-xs text-slate-400">Read the document here without leaving this page.</p>
                </div>

                <button
                    type="button"
                    onclick="window.closeToolkitReader()"
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50"
                    aria-label="Close document reader"
                >
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>

            {{-- BODY --}}
            <div id="toolkitReaderBody" class="min-h-0 flex-1 overflow-auto bg-slate-100">
                <div id="toolkitReaderLoading" class="flex min-h-full items-center justify-center p-8">
                    <div class="text-center">
                        <div class="mx-auto h-8 w-8 animate-spin rounded-full border-2 border-brand-600/20 border-t-brand-600"></div>
                        <p class="mt-3 text-sm text-slate-400">Opening file...</p>
                    </div>
                </div>

                {{-- PDF --}}
                <div id="toolkitReaderPdfViewer" class="hidden min-h-full bg-slate-200 p-4 sm:p-6">
                    <div id="toolkitReaderPdfPages" class="mx-auto flex w-fit flex-col items-center gap-5"></div>
                </div>

                {{-- DOCX --}}
                <div id="toolkitReaderDocxViewer" class="hidden min-h-full p-4 sm:p-8">
                    <article id="toolkitReaderDocxContent" class="mx-auto min-h-full max-w-4xl rounded-xl bg-white p-6 shadow-sm sm:p-10"></article>
                </div>

                {{-- XLS/XLSX/CSV --}}
                <div id="toolkitReaderSheetViewer" class="hidden min-h-full p-3 sm:p-6">
                    <div id="toolkitReaderSheetContent" class="mx-auto max-w-full overflow-auto rounded-xl border border-slate-200 bg-white shadow-sm"></div>
                </div>

                {{-- PPTX --}}
                <div id="toolkitReaderPptxViewer" class="hidden min-h-full bg-slate-200 p-3 sm:p-6">
                    <div id="toolkitReaderPptxContent" class="mx-auto min-h-[500px] w-full max-w-5xl overflow-auto rounded-xl bg-white shadow-sm"></div>
                </div>

                {{-- IMAGE --}}
                <div id="toolkitReaderImageViewer" class="hidden min-h-full items-center justify-center p-4 sm:p-8">
                    <img id="toolkitReaderImageContent" src="" alt="" class="max-h-full max-w-full rounded-xl bg-white object-contain shadow-xl">
                </div>

                {{-- TEXT --}}
                <div id="toolkitReaderTextViewer" class="hidden min-h-full p-4 sm:p-8">
                    <pre id="toolkitReaderTextContent" class="mx-auto max-w-5xl whitespace-pre-wrap break-words rounded-xl bg-white p-5 font-mono text-sm leading-6 text-slate-800 shadow-sm"></pre>
                </div>

                {{-- GENERIC BROWSER PREVIEW --}}
                <div id="toolkitReaderGenericViewer" class="hidden min-h-full bg-white">
                    <iframe id="toolkitReaderGenericFrame" src="about:blank" class="h-full min-h-[70vh] w-full border-0" title="File preview"></iframe>
                </div>

                {{-- UNSUPPORTED --}}
                <div id="toolkitReaderUnsupportedViewer" class="hidden min-h-full items-center justify-center p-8">
                    <div class="max-w-md text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                            <x-icon name="file" class="h-7 w-7" />
                        </div>
                        <h3 class="mt-4 text-base font-semibold text-slate-800">Preview not available</h3>
                        <p id="toolkitReaderUnsupportedText" class="mt-1 text-sm leading-6 text-slate-400">This file format cannot be rendered directly in the browser.</p>
                    </div>
                </div>

                <div id="toolkitReaderError" class="hidden min-h-full items-center justify-center p-8">
                    <div class="max-w-md text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                            <x-icon name="alert-circle" class="h-7 w-7" />
                        </div>
                        <h3 class="mt-4 text-base font-semibold text-slate-800">Unable to preview this file</h3>
                        <p id="toolkitReaderErrorText" class="mt-1 text-sm leading-6 text-slate-400">The file could not be opened.</p>
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="flex shrink-0 items-center justify-between border-t border-slate-100 bg-white px-4 py-3 sm:px-6">
                <div id="toolkitReaderStatus" class="truncate pr-4 text-xs text-slate-400">Document preview</div>

                <div class="flex shrink-0 items-center gap-2">
                    <button type="button" id="toolkitReaderDownload" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">
                        <x-icon name="download" class="h-4 w-4" />
                        Download
                    </button>

                    <button type="button" id="toolkitReaderPrint" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">
                        <x-icon name="printer" class="h-4 w-4" />
                        Print
                    </button>

                    <button type="button" id="toolkitReaderPrev" class="hidden inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40">
                        <x-icon name="chevron-left" class="h-4 w-4" />
                        Previous
                    </button>

                    <span id="toolkitReaderPageIndicator" class="hidden rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700">1 / 1</span>

                    <button type="button" id="toolkitReaderNext" class="hidden inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40">
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
            const modal = document.getElementById('toolkitReader');
            const loading = document.getElementById('toolkitReaderLoading');
            const title = document.getElementById('toolkitReaderTitle');
            const downloadButton = document.getElementById('toolkitReaderDownload');
            const printButton = document.getElementById('toolkitReaderPrint');

            const meta = document.getElementById('toolkitReaderMeta');
            const status = document.getElementById('toolkitReaderStatus');

            const pdfViewer = document.getElementById('toolkitReaderPdfViewer');
            const pdfPages = document.getElementById('toolkitReaderPdfPages');

            const docxViewer = document.getElementById('toolkitReaderDocxViewer');
            const docxContent = document.getElementById('toolkitReaderDocxContent');

            const sheetViewer = document.getElementById('toolkitReaderSheetViewer');
            const sheetContent = document.getElementById('toolkitReaderSheetContent');

            const pptxViewer = document.getElementById('toolkitReaderPptxViewer');
            const pptxContent = document.getElementById('toolkitReaderPptxContent');

            const imageViewer = document.getElementById('toolkitReaderImageViewer');
            const imageContent = document.getElementById('toolkitReaderImageContent');

            const textViewer = document.getElementById('toolkitReaderTextViewer');
            const textContent = document.getElementById('toolkitReaderTextContent');

            const genericViewer = document.getElementById('toolkitReaderGenericViewer');
            const genericFrame = document.getElementById('toolkitReaderGenericFrame');

            const unsupportedViewer = document.getElementById('toolkitReaderUnsupportedViewer');
            const unsupportedText = document.getElementById('toolkitReaderUnsupportedText');

            const errorViewer = document.getElementById('toolkitReaderError');
            const errorText = document.getElementById('toolkitReaderErrorText');

            const prev = document.getElementById('toolkitReaderPrev');
            const next = document.getElementById('toolkitReaderNext');
            const indicator = document.getElementById('toolkitReaderPageIndicator');

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

            const openDocument = async (itemId) => {
                const file = window.toolkitReaderFiles?.[itemId];

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
                    console.error('Toolkit document preview error:', error);

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

            window.openToolkitReader = openDocument;
            window.closeToolkitReader = closeDocument;

            downloadButton.addEventListener('click', () => downloadFile(currentFile));
            printButton.addEventListener('click', printCurrent);

            prev.addEventListener('click', () => {
                if (!pdf || pdfPage <= 1) return;

                pdfPage--;

                const page = document.querySelector(`#toolkitReaderPdfPages [data-page="${pdfPage}"]`);
                page?.scrollIntoView({ behavior: 'smooth', block: 'start' });

                setPageControls(pdfPage, pdf.numPages);
            });

            next.addEventListener('click', () => {
                if (!pdf || pdfPage >= pdf.numPages) return;

                pdfPage++;

                const page = document.querySelector(`#toolkitReaderPdfPages [data-page="${pdfPage}"]`);
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
