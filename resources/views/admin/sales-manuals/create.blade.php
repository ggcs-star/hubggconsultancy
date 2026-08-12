<x-layout title="Create Sales Manual">

    {{-- Header --}}
    <div class="flex flex-col gap-1">

        <div class="flex items-center gap-3">

            <a
                href="{{ route('admin.manuals.index') }}"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-secondary transition hover:bg-surface-alt hover:text-primary"
                title="Back"
            >
                <x-icon name="arrow-left" class="h-5 w-5" />
            </a>

            <div>
                <h1 class="text-2xl font-semibold text-secondary-dark">
                    Create Sales Manual
                </h1>

                <p class="mt-1 text-sm text-secondary">
                    Create a manual, guide, cheat sheet, FAQ, SOP or sales script.
                </p>
            </div>

        </div>

    </div>


    {{-- Errors --}}
    @if ($errors->any())

        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4">

            <div class="flex items-start gap-3">

                <x-icon
                    name="information-circle"
                    class="mt-0.5 h-5 w-5 shrink-0 text-red-600"
                />

                <div>

                    <p class="text-sm font-semibold text-red-700">
                        Please fix the following errors:
                    </p>

                    <ul class="mt-2 list-disc space-y-1 pl-5 text-xs text-red-600">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    {{-- Main Form --}}
    <form
        method="POST"
        action="{{ route('admin.manuals.store') }}"
        enctype="multipart/form-data"
        class="mt-6"
    >

        @csrf


        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">


            {{-- LEFT --}}
            <div class="space-y-6 xl:col-span-2">


                {{-- Basic Information --}}
                <div class="rounded-xl border border-app-border bg-white">

                    <div class="border-b border-app-border px-6 py-4">

                        <h2 class="text-base font-semibold text-secondary-dark">
                            Basic Information
                        </h2>

                        <p class="mt-1 text-xs text-secondary">
                            Add the basic information for this sales resource.
                        </p>

                    </div>


                    <div class="p-6">

                        {{-- Title --}}
                        <div>

                            <x-input-label
                                for="title"
                                value="Title *"
                                class="text-xs font-semibold uppercase tracking-wide"
                            />

                            <x-text-input
                                id="title"
                                name="title"
                                value="{{ old('title') }}"
                                class="mt-1.5"
                                placeholder="e.g. Complete Product Sales Guide"
                                required
                            />

                        </div>


                        {{-- Type + Category --}}
                        <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">

                            <div>

                                <x-input-label
                                    for="type"
                                    value="Resource Type *"
                                    class="text-xs font-semibold uppercase tracking-wide"
                                />

                                <select
                                    id="type"
                                    name="type"
                                    required
                                    class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                                >

                                    <option value="">
                                        Select Type
                                    </option>

                                    <option
                                        value="manual"
                                        @selected(old('type') === 'manual')
                                    >
                                        Manual
                                    </option>

                                    <option
                                        value="guide"
                                        @selected(old('type') === 'guide')
                                    >
                                        Sales Guide
                                    </option>

                                    <option
                                        value="cheat_sheet"
                                        @selected(old('type') === 'cheat_sheet')
                                    >
                                        Cheat Sheet
                                    </option>

                                    <option
                                        value="faq"
                                        @selected(old('type') === 'faq')
                                    >
                                        FAQ
                                    </option>

                                    <option
                                        value="sop"
                                        @selected(old('type') === 'sop')
                                    >
                                        SOP
                                    </option>

                                    <option
                                        value="script"
                                        @selected(old('type') === 'script')
                                    >
                                        Sales Script
                                    </option>

                                </select>

                            </div>


                            <div>

                                <x-input-label
                                    for="category"
                                    value="Category"
                                    class="text-xs font-semibold uppercase tracking-wide"
                                />

                                <x-text-input
                                    id="category"
                                    name="category"
                                    value="{{ old('category') }}"
                                    class="mt-1.5"
                                    placeholder="e.g. Product, Pricing, Objection Handling"
                                />

                            </div>

                        </div>


                        {{-- Description --}}
                        <div class="mt-5">

                            <x-input-label
                                for="description"
                                value="Short Description"
                                class="text-xs font-semibold uppercase tracking-wide"
                            />

                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                maxlength="2000"
                                placeholder="Briefly explain what this resource is about..."
                                class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                            >{{ old('description') }}</textarea>

                        </div>

                    </div>

                </div>


                {{-- Content Editor --}}
                <div class="rounded-xl border border-app-border bg-white">

                    <div class="border-b border-app-border px-6 py-4">

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                            <div>

                                <h2 class="text-base font-semibold text-secondary-dark">
                                    Content
                                </h2>

                                <p class="mt-1 text-xs text-secondary">
                                    Write the content yourself or use a ready-made template.
                                </p>

                            </div>


                            {{-- Template --}}
                            <div>

                                <select
                                    id="template"
                                    class="rounded-lg border-app-border text-xs shadow-sm focus:border-primary focus:ring-primary"
                                >

                                    <option value="">
                                        Insert Template
                                    </option>

                                    <option value="cheat_sheet">
                                        Cheat Sheet Template
                                    </option>

                                    <option value="guide">
                                        Sales Guide Template
                                    </option>

                                    <option value="faq">
                                        FAQ Template
                                    </option>

                                    <option value="sop">
                                        SOP Template
                                    </option>

                                    <option value="script">
                                        Sales Script Template
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>


                    <div class="p-6">

                        <textarea
                            id="content"
                            name="content"
                            rows="22"
                            placeholder="Write your sales content here..."
                            class="w-full rounded-lg border-app-border font-mono text-sm leading-6 shadow-sm focus:border-primary focus:ring-primary"
                        >{{ old('content') }}</textarea>


                        <div class="mt-3 flex items-start gap-2 rounded-lg bg-surface-alt p-3">

                            <x-icon
                                name="information-circle"
                                class="mt-0.5 h-4 w-4 shrink-0 text-primary"
                            />

                            <p class="text-xs leading-5 text-secondary">
                                You can create your own content here. If you don't want to write
                                anything, you can simply upload documents below.
                            </p>

                        </div>

                    </div>

                </div>


                {{-- Attachments --}}
                <div class="rounded-xl border border-app-border bg-white">

                    <div class="border-b border-app-border px-6 py-4">

                        <h2 class="text-base font-semibold text-secondary-dark">
                            Documents & Attachments
                        </h2>

                        <p class="mt-1 text-xs text-secondary">
                            Upload one or multiple supporting files.
                        </p>

                    </div>


                    <div class="p-6">

                        <label
                            for="attachments"
                            class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-app-border px-6 py-10 text-center transition hover:border-primary hover:bg-primary-light/30"
                        >

                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-primary-light text-primary">

                                <x-icon
                                    name="paper-clip"
                                    class="h-7 w-7"
                                />

                            </div>


                            <p class="mt-4 text-sm font-semibold text-secondary-dark">
                                Upload documents
                            </p>


                            <p class="mt-1 max-w-lg text-xs leading-5 text-secondary">

                                PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, images,
                                ZIP, TXT, CSV and other supported files.

                            </p>


                            <span class="mt-4 inline-flex items-center gap-2 rounded-lg border border-app-border bg-white px-4 py-2 text-xs font-medium text-secondary-dark">

                                <x-icon
                                    name="upload"
                                    class="h-4 w-4"
                                />

                                Choose Files

                            </span>


                            <input
                                id="attachments"
                                type="file"
                                name="attachments[]"
                                multiple
                                class="hidden"
                            >

                        </label>


                        {{-- Selected Files --}}
                        <div
                            id="file-list"
                            class="mt-4 hidden"
                        >

                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-secondary">
                                Selected Files
                            </p>

                            <div
                                id="selected-files"
                                class="space-y-2"
                            ></div>

                        </div>


                        <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3">

                            <div class="flex items-start gap-2">

                                <x-icon
                                    name="information-circle"
                                    class="mt-0.5 h-4 w-4 shrink-0 text-amber-600"
                                />

                                <p class="text-xs leading-5 text-amber-700">
                                    Maximum file size is 50MB per file.
                                    Multiple files can be uploaded together.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Cover Image --}}
                <div class="rounded-xl border border-app-border bg-white">

                    <div class="border-b border-app-border px-6 py-4">

                        <h2 class="text-base font-semibold text-secondary-dark">
                            Cover Image
                        </h2>

                        <p class="mt-1 text-xs text-secondary">
                            Optional image displayed with the sales manual.
                        </p>

                    </div>


                    <div class="p-6">

                        <label
                            for="cover_image"
                            class="block cursor-pointer rounded-xl border border-dashed border-app-border p-6 text-center transition hover:border-primary hover:bg-primary-light/30"
                        >

                            <x-icon
                                name="image"
                                class="mx-auto h-8 w-8 text-primary"
                            />

                            <p class="mt-2 text-sm font-medium text-secondary-dark">
                                Choose Cover Image
                            </p>

                            <p class="mt-1 text-xs text-secondary">
                                JPG, JPEG, PNG or WEBP up to 5MB
                            </p>

                            <input
                                id="cover_image"
                                type="file"
                                name="cover_image"
                                accept=".jpg,.jpeg,.png,.webp"
                                class="mt-4 block w-full text-sm text-secondary"
                            >

                        </label>

                    </div>

                </div>

            </div>


            {{-- RIGHT --}}
            <div class="space-y-6">


                {{-- Publishing --}}
                <div class="rounded-xl border border-app-border bg-white">

                    <div class="border-b border-app-border px-5 py-4">

                        <h2 class="text-base font-semibold text-secondary-dark">
                            Publishing
                        </h2>

                    </div>


                    <div class="p-5">

                        <label
                            for="status"
                            class="text-xs font-semibold uppercase tracking-wide text-secondary"
                        >
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                        >

                            <option
                                value="draft"
                                @selected(old('status', 'draft') === 'draft')
                            >
                                Draft
                            </option>

                            <option
                                value="published"
                                @selected(old('status') === 'published')
                            >
                                Published
                            </option>

                        </select>


                        {{-- Active --}}
                        <label class="mt-5 flex cursor-pointer items-start gap-3">

                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                @checked(old('is_active', true))
                                class="mt-0.5 h-4 w-4 rounded border-app-border text-primary focus:ring-primary"
                            >

                            <div>

                                <p class="text-sm font-medium text-secondary-dark">
                                    Active
                                </p>

                                <p class="mt-1 text-xs leading-5 text-secondary">
                                    Users can see this resource when it is active.
                                </p>

                            </div>

                        </label>


                        {{-- Featured --}}
                        <label class="mt-5 flex cursor-pointer items-start gap-3">

                            <input
                                type="checkbox"
                                name="is_featured"
                                value="1"
                                @checked(old('is_featured'))
                                class="mt-0.5 h-4 w-4 rounded border-app-border text-primary focus:ring-primary"
                            >

                            <div>

                                <p class="text-sm font-medium text-secondary-dark">
                                    Featured
                                </p>

                                <p class="mt-1 text-xs leading-5 text-secondary">
                                    Highlight this resource for users.
                                </p>

                            </div>

                        </label>


                        {{-- Pinned --}}
                        <label class="mt-5 flex cursor-pointer items-start gap-3">

                            <input
                                type="checkbox"
                                name="is_pinned"
                                value="1"
                                @checked(old('is_pinned'))
                                class="mt-0.5 h-4 w-4 rounded border-app-border text-primary focus:ring-primary"
                            >

                            <div>

                                <p class="text-sm font-medium text-secondary-dark">
                                    Pinned
                                </p>

                                <p class="mt-1 text-xs leading-5 text-secondary">
                                    Keep this resource at the top of the list.
                                </p>

                            </div>

                        </label>

                    </div>

                </div>


                {{-- Ordering --}}
                <div class="rounded-xl border border-app-border bg-white">

                    <div class="border-b border-app-border px-5 py-4">

                        <h2 class="text-base font-semibold text-secondary-dark">
                            Display Order
                        </h2>

                    </div>


                    <div class="p-5">

                        <x-input-label
                            for="sort_order"
                            value="Sort Order"
                            class="text-xs font-semibold uppercase tracking-wide"
                        />

                        <x-text-input
                            id="sort_order"
                            type="number"
                            name="sort_order"
                            value="{{ old('sort_order', 0) }}"
                            min="0"
                            class="mt-1.5"
                        />

                        <p class="mt-1.5 text-xs leading-5 text-secondary">
                            Lower numbers appear first.
                        </p>

                    </div>

                </div>


                {{-- Quick Templates --}}
                <div class="rounded-xl border border-app-border bg-white">

                    <div class="border-b border-app-border px-5 py-4">

                        <h2 class="text-base font-semibold text-secondary-dark">
                            Quick Templates
                        </h2>

                    </div>


                    <div class="space-y-2 p-5">

                        <button
                            type="button"
                            data-template="cheat_sheet"
                            class="template-button flex w-full items-center gap-3 rounded-lg border border-app-border p-3 text-left transition hover:border-primary hover:bg-primary-light/30"
                        >

                            <x-icon
                                name="list"
                                class="h-5 w-5 text-primary"
                            />

                            <span>
                                <span class="block text-sm font-medium text-secondary-dark">
                                    Cheat Sheet
                                </span>

                                <span class="block text-xs text-secondary">
                                    Quick reference format
                                </span>
                            </span>

                        </button>


                        <button
                            type="button"
                            data-template="faq"
                            class="template-button flex w-full items-center gap-3 rounded-lg border border-app-border p-3 text-left transition hover:border-primary hover:bg-primary-light/30"
                        >

                            <x-icon
                                name="help-circle"
                                class="h-5 w-5 text-primary"
                            />

                            <span>
                                <span class="block text-sm font-medium text-secondary-dark">
                                    FAQ
                                </span>

                                <span class="block text-xs text-secondary">
                                    Questions and answers
                                </span>
                            </span>

                        </button>


                        <button
                            type="button"
                            data-template="script"
                            class="template-button flex w-full items-center gap-3 rounded-lg border border-app-border p-3 text-left transition hover:border-primary hover:bg-primary-light/30"
                        >

                            <x-icon
                                name="pencil"
                                class="h-5 w-5 text-primary"
                            />

                            <span>
                                <span class="block text-sm font-medium text-secondary-dark">
                                    Sales Script
                                </span>

                                <span class="block text-xs text-secondary">
                                    Ready conversation structure
                                </span>
                            </span>

                        </button>


                        <button
                            type="button"
                            data-template="sop"
                            class="template-button flex w-full items-center gap-3 rounded-lg border border-app-border p-3 text-left transition hover:border-primary hover:bg-primary-light/30"
                        >

                            <x-icon
                                name="check-circle"
                                class="h-5 w-5 text-primary"
                            />

                            <span>
                                <span class="block text-sm font-medium text-secondary-dark">
                                    SOP
                                </span>

                                <span class="block text-xs text-secondary">
                                    Step-by-step process
                                </span>
                            </span>

                        </button>

                    </div>

                </div>


                {{-- Admin Info --}}
                <div class="rounded-xl border border-primary/20 bg-primary-light p-5">

                    <div class="flex items-start gap-3">

                        <x-icon
                            name="information-circle"
                            class="mt-0.5 h-5 w-5 shrink-0 text-primary"
                        />

                        <div>

                            <p class="text-sm font-semibold text-secondary-dark">
                                Flexible Content
                            </p>

                            <p class="mt-1 text-xs leading-5 text-secondary">

                                You don't need to upload a document.
                                Admin can create the complete resource directly
                                inside the content editor.

                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Footer --}}
        <div class="mt-6 flex flex-col-reverse gap-3 rounded-xl border border-app-border bg-white px-6 py-4 sm:flex-row sm:items-center sm:justify-end">

            <a
                href="{{ route('admin.manuals.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-app-border bg-white px-5 py-2.5 text-sm font-medium text-secondary-dark transition hover:bg-surface-alt"
            >

                <x-icon
                    name="arrow-left"
                    class="h-4 w-4"
                />

                Cancel

            </a>


            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90"
            >

                <x-icon
                    name="check"
                    class="h-4 w-4"
                />

                Create Sales Manual

            </button>

        </div>

    </form>


    {{-- Template Script --}}
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const content = document.getElementById('content');
            const type = document.getElementById('type');
            const template = document.getElementById('template');

            const templates = {

                cheat_sheet: `CHEAT SHEET

Topic:
[Enter topic]

Quick Summary:
[Enter short summary]

Key Points:
• Point 1
• Point 2
• Point 3

Important Information:
[Enter important information]

Do:
• 
• 
• 

Don't:
• 
• 
• 

Quick Reference:
[Add quick reference information]
`,

                guide: `SALES GUIDE

Introduction:
[Write introduction]

Product / Service:
[Enter product or service details]

Target Customer:
[Describe ideal customer]

Key Benefits:
1. 
2. 
3. 

Sales Process:
Step 1:
Step 2:
Step 3:
Step 4:

Important Talking Points:
• 
• 
• 

Common Objections:
Objection:
Response:

Closing:
[Write closing strategy]
`,

                faq: `FREQUENTLY ASKED QUESTIONS

Q1. [Question]
Answer:
[Answer]

Q2. [Question]
Answer:
[Answer]

Q3. [Question]
Answer:
[Answer]

Q4. [Question]
Answer:
[Answer]

Q5. [Question]
Answer:
[Answer]
`,

                sop: `STANDARD OPERATING PROCEDURE

Process Name:
[Enter process name]

Purpose:
[Explain purpose]

Prerequisites:
• 
• 
• 

Step 1:
[Instructions]

Step 2:
[Instructions]

Step 3:
[Instructions]

Step 4:
[Instructions]

Important Notes:
• 
• 
• 

Expected Result:
[Describe expected result]
`,

                script: `SALES SCRIPT

Opening:
"Hello, [Customer Name]..."

Discovery Questions:
1. 
2. 
3. 

Problem Identification:
[Write questions / conversation]

Product Introduction:
[Sales pitch]

Key Benefits:
• 
• 
• 

Objection Handling:

Customer:
"[Objection]"

Salesperson:
"[Response]"

Closing:
"[Closing statement]"

Follow-up:
"[Follow-up message]"
`
            };


            function applyTemplate(name) {

                if (!templates[name]) {
                    return;
                }

                if (
                    content.value.trim() !== '' &&
                    !confirm('Existing content will be replaced. Continue?')
                ) {
                    return;
                }

                content.value = templates[name];

                if (type) {
                    type.value = name;
                }

                content.focus();

            }


            if (template) {

                template.addEventListener('change', function () {

                    applyTemplate(this.value);

                    this.value = '';

                });

            }


            document.querySelectorAll('.template-button').forEach(function (button) {

                button.addEventListener('click', function () {

                    applyTemplate(this.dataset.template);

                });

            });


            {{-- File preview --}}

            const attachments = document.getElementById('attachments');
            const fileList = document.getElementById('file-list');
            const selectedFiles = document.getElementById('selected-files');

            if (attachments) {

                attachments.addEventListener('change', function () {

                    selectedFiles.innerHTML = '';

                    if (!this.files.length) {

                        fileList.classList.add('hidden');

                        return;

                    }

                    fileList.classList.remove('hidden');


                    Array.from(this.files).forEach(function (file) {

                        const row = document.createElement('div');

                        row.className =
                            'flex items-center justify-between gap-3 rounded-lg border border-app-border bg-surface-alt px-3 py-2';


                        const left = document.createElement('div');

                        left.className =
                            'flex min-w-0 items-center gap-2';


                        left.innerHTML = `
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white">
                                <span class="text-xs font-semibold text-primary">
                                    ${file.name.split('.').pop().toUpperCase()}
                                </span>
                            </span>

                            <span class="min-w-0">
                                <span class="block truncate text-xs font-medium text-secondary-dark">
                                    ${file.name}
                                </span>

                                <span class="block text-[11px] text-secondary">
                                    ${(file.size / 1024 / 1024).toFixed(2)} MB
                                </span>
                            </span>
                        `;


                        row.appendChild(left);

                        selectedFiles.appendChild(row);

                    });

                });

            }

        });

    </script>

</x-layout>