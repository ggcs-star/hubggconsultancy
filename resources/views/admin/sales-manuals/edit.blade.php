<x-layout title="Edit Sales Manual">

    {{-- Header --}}
    <div>

        <a
            href="{{ route('admin.manuals.index') }}"
            class="inline-flex items-center gap-2 text-sm text-secondary transition hover:text-secondary-dark">

            <x-icon name="arrow-left" class="h-4 w-4" />

            Back to Sales Manuals

        </a>

        <h1 class="mt-4 text-2xl font-semibold text-secondary-dark">
            Edit Sales Manual
        </h1>

        <p class="mt-1 text-sm text-secondary">
            Update the content, files, visibility and publishing settings.
        </p>

    </div>


    {{-- Success --}}
    @if (session('success'))

        <div class="mt-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>

    @endif


    {{-- Errors --}}
    @if ($errors->any())

        <div class="mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3">

            <ul class="list-disc space-y-1 pl-5 text-sm text-red-700">

                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    <div class="mt-6 rounded-xl border border-app-border bg-white">

        <form
            method="POST"
            action="{{ route('admin.manuals.update', $manual) }}"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')


            {{-- Basic Information --}}
            <div class="border-b border-app-border p-6">

                <h2 class="text-base font-semibold text-secondary-dark">
                    Basic Information
                </h2>

                <div class="mt-5">

                    <x-input-label
                        for="title"
                        value="Title *"
                        class="uppercase text-xs tracking-wide" />

                    <x-text-input
                        id="title"
                        name="title"
                        value="{{ old('title', $manual->title) }}"
                        class="mt-1.5"
                        required />

                </div>


                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">

                    {{-- Type --}}
                    <div>

                        <x-input-label
                            for="type"
                            value="Content Type *"
                            class="uppercase text-xs tracking-wide" />

                        <select
                            id="type"
                            name="type"
                            required
                            class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">

                            <option value="manual" @selected(old('type', $manual->type) === 'manual')>
                                Manual
                            </option>

                            <option value="guide" @selected(old('type', $manual->type) === 'guide')>
                                Sales Guide
                            </option>

                            <option value="cheat_sheet" @selected(old('type', $manual->type) === 'cheat_sheet')>
                                Cheat Sheet
                            </option>

                            <option value="faq" @selected(old('type', $manual->type) === 'faq')>
                                FAQ
                            </option>

                            <option value="sop" @selected(old('type', $manual->type) === 'sop')>
                                SOP
                            </option>

                            <option value="script" @selected(old('type', $manual->type) === 'script')>
                                Sales Script
                            </option>

                        </select>

                    </div>


                    {{-- Category --}}
                    <div>

                        <x-input-label
                            for="category"
                            value="Category"
                            class="uppercase text-xs tracking-wide" />

                        <x-text-input
                            id="category"
                            name="category"
                            value="{{ old('category', $manual->category) }}"
                            class="mt-1.5"
                            placeholder="e.g. Product, Pricing, Objections" />

                    </div>


                    {{-- Language --}}
                    <div>

                        <x-input-label
                            for="language"
                            value="Language *"
                            class="uppercase text-xs tracking-wide" />

                        <select
                            id="language"
                            name="language"
                            required
                            class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">

                            <option value="english" @selected(old('language', $manual->language) === 'english')>
                                English
                            </option>

                            <option value="hindi" @selected(old('language', $manual->language) === 'hindi')>
                                Hindi
                            </option>

                            <option value="gujarati" @selected(old('language', $manual->language) === 'gujarati')>
                                Gujarati
                            </option>

                        </select>

                    </div>

                </div>


                {{-- Description --}}
                <div class="mt-5">

                    <x-input-label
                        for="description"
                        value="Short Description"
                        class="uppercase text-xs tracking-wide" />

                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        class="mt-1.5 w-full resize-none rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('description', $manual->description) }}</textarea>

                </div>

            </div>


            {{-- Cover Image --}}
            <div class="border-b border-app-border p-6">

                <h2 class="text-base font-semibold text-secondary-dark">
                    Cover Image
                </h2>


                @if ($manual->cover_image)

                    <div class="mt-4 flex items-center gap-4">

                        <img
                            src="{{ asset('storage/' . $manual->cover_image) }}"
                            alt="{{ $manual->title }}"
                            class="h-24 w-24 rounded-xl object-cover border border-app-border">

                        <div>

                            <p class="text-sm font-medium text-secondary-dark">
                                Current Cover Image
                            </p>

                            <p class="mt-1 text-xs text-secondary">
                                Upload a new image below to replace it.
                            </p>

                        </div>

                    </div>

                @endif


                <input
                    type="file"
                    name="cover_image"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    class="mt-5 block w-full text-sm text-secondary">

            </div>


            {{-- Content --}}
            <div class="border-b border-app-border p-6">

                <h2 class="text-base font-semibold text-secondary-dark">
                    Sales Guidance Content
                </h2>

                <div class="mt-5">

                    <x-input-label
                        for="content"
                        value="Content"
                        class="uppercase text-xs tracking-wide" />

                    <textarea
                        id="content"
                        name="content"
                        rows="16"
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('content', $manual->content) }}</textarea>

                </div>

            </div>


            {{-- Existing Attachments --}}
            <div class="border-b border-app-border p-6">

                <h2 class="text-base font-semibold text-secondary-dark">
                    Existing Attachments
                </h2>

                @if ($manual->attachments->count())

                    <div class="mt-4 divide-y divide-app-border rounded-xl border border-app-border">

                        @foreach ($manual->attachments as $attachment)

                            <div class="flex items-center justify-between gap-4 p-4">

                                <div class="flex min-w-0 items-center gap-3">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light">

                                        <x-icon
                                            name="paper-clip"
                                            class="h-5 w-5 text-primary" />

                                    </div>

                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-medium text-secondary-dark">
                                            {{ $attachment->file_name }}
                                        </p>

                                        <p class="mt-1 text-xs text-secondary">

                                            {{ strtoupper($attachment->file_type ?? 'FILE') }}

                                            @if ($attachment->file_size)
                                                · {{ number_format($attachment->file_size / 1024 / 1024, 2) }} MB
                                            @endif

                                        </p>

                                    </div>

                                </div>


                                <div class="flex shrink-0 items-center gap-1">

                                    <a
                                        href="{{ asset('storage/' . $attachment->file_path) }}"
                                        target="_blank"
                                        class="rounded-lg p-2 text-secondary transition hover:bg-surface-alt hover:text-primary"
                                        title="Open">

                                        <x-icon
                                            name="eye"
                                            class="h-4 w-4" />

                                    </a>


                                    <form
                                        method="POST"
                                        action="{{ route('admin.manuals.attachments.delete', $attachment) }}"
                                        onsubmit="return confirm('Delete this attachment?');">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Delete"
                                            class="rounded-lg p-2 text-secondary transition hover:bg-red-50 hover:text-red-600">

                                            <x-icon
                                                name="trash"
                                                class="h-4 w-4" />

                                        </button>

                                    </form>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <p class="mt-4 rounded-lg bg-surface-alt p-4 text-sm text-secondary">
                        No attachments uploaded.
                    </p>

                @endif


                {{-- New Attachments --}}
                <div class="mt-5">

                    <x-input-label
                        for="attachments"
                        value="Add New Files"
                        class="uppercase text-xs tracking-wide" />

                    <input
                        id="attachments"
                        type="file"
                        name="attachments[]"
                        multiple
                        class="mt-1.5 block w-full text-sm text-secondary">

                    <p class="mt-1.5 text-xs text-secondary">
                        You can select multiple files. Maximum 50MB per file.
                    </p>

                </div>

            </div>


            {{-- Visibility --}}
            <div class="border-b border-app-border p-6">

                <h2 class="text-base font-semibold text-secondary-dark">
                    Visibility & Organization
                </h2>


                {{-- Status --}}
                <div class="mt-5">

                    <x-input-label
                        for="status"
                        value="Publication Status *"
                        class="uppercase text-xs tracking-wide" />

                    <select
                        id="status"
                        name="status"
                        required
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">

                        <option
                            value="draft"
                            @selected(old('status', $manual->status) === 'draft')>
                            Draft
                        </option>

                        <option
                            value="published"
                            @selected(old('status', $manual->status) === 'published')>
                            Published
                        </option>

                    </select>

                </div>


                {{-- Sort --}}
                <div class="mt-5">

                    <x-input-label
                        for="sort_order"
                        value="Sort Order"
                        class="uppercase text-xs tracking-wide" />

                    <x-text-input
                        id="sort_order"
                        name="sort_order"
                        type="number"
                        min="0"
                        value="{{ old('sort_order', $manual->sort_order) }}"
                        class="mt-1.5" />

                </div>


                {{-- Active --}}
                <div class="mt-5 rounded-lg border border-app-border bg-surface-alt p-4">

                    <label class="flex cursor-pointer items-center justify-between gap-4">

                        <div>

                            <p class="text-sm font-medium text-secondary-dark">
                                Active
                            </p>

                            <p class="mt-1 text-xs text-secondary">
                                Users can see this resource when it is active and published.
                            </p>

                        </div>

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked(old('is_active', $manual->is_active))
                            class="h-4 w-4 rounded border-app-border text-primary focus:ring-primary">

                    </label>

                </div>


                {{-- Featured --}}
                <div class="mt-3 rounded-lg border border-app-border bg-surface-alt p-4">

                    <label class="flex cursor-pointer items-center justify-between gap-4">

                        <div>

                            <p class="text-sm font-medium text-secondary-dark">
                                Featured
                            </p>

                            <p class="mt-1 text-xs text-secondary">
                                Highlight this resource for users.
                            </p>

                        </div>

                        <input
                            type="checkbox"
                            name="is_featured"
                            value="1"
                            @checked(old('is_featured', $manual->is_featured))
                            class="h-4 w-4 rounded border-app-border text-primary focus:ring-primary">

                    </label>

                </div>


                {{-- Pinned --}}
                <div class="mt-3 rounded-lg border border-app-border bg-surface-alt p-4">

                    <label class="flex cursor-pointer items-center justify-between gap-4">

                        <div>

                            <p class="text-sm font-medium text-secondary-dark">
                                Pinned
                            </p>

                            <p class="mt-1 text-xs text-secondary">
                                Keep this resource at the top.
                            </p>

                        </div>

                        <input
                            type="checkbox"
                            name="is_pinned"
                            value="1"
                            @checked(old('is_pinned', $manual->is_pinned))
                            class="h-4 w-4 rounded border-app-border text-primary focus:ring-primary">

                    </label>

                </div>

            </div>


            {{-- Footer --}}
            <div class="flex flex-col-reverse gap-3 border-t border-app-border px-6 py-4 sm:flex-row sm:justify-end">

                <a
                    href="{{ route('admin.manuals.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-app-border bg-white px-4 py-2.5 text-sm font-medium text-secondary-dark transition hover:bg-surface-alt">

                    Cancel

                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90">

                    <x-icon name="check" class="h-4 w-4" />

                    Update Sales Manual

                </button>

            </div>

        </form>

    </div>

</x-layout>