<x-layout title="Sales Manual">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <a
                href="{{ route('user.manuals') }}"
                class="inline-flex items-center gap-2 text-sm text-secondary transition hover:text-secondary-dark">

                <x-icon
                    name="arrow-left"
                    class="h-4 w-4" />

                Back to Sales Manuals
            </a>

            <h1 class="mt-4 text-2xl font-semibold text-secondary-dark">
                {{ $manual->title }}
            </h1>

            <p class="mt-1 text-sm text-secondary">
                View this sales resource.
            </p>
        </div>

    </div>


    {{-- Status --}}
    <div class="mt-6 flex flex-wrap gap-2">

        <span
            class="rounded-full px-3 py-1 text-xs font-medium
            {{ $manual->status === 'published'
                ? 'bg-green-50 text-green-700'
                : 'bg-yellow-50 text-yellow-700' }}">

            {{ ucfirst($manual->status) }}

        </span>


        @if ($manual->is_active)

            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">
                Active
            </span>

        @else

            <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">
                Inactive
            </span>

        @endif


        @if ($manual->is_featured)

            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                Featured
            </span>

        @endif


        @if ($manual->is_pinned)

            <span class="rounded-full bg-purple-50 px-3 py-1 text-xs font-medium text-purple-700">
                Pinned
            </span>

        @endif

    </div>


    {{-- Main --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">


        {{-- Left --}}
        <div class="space-y-6 lg:col-span-2">


            {{-- Cover Image --}}
            @if ($manual->cover_image)

                <div class="overflow-hidden rounded-xl border border-app-border bg-white">

                    <img
                        src="{{ asset('storage/' . $manual->cover_image) }}"
                        alt="{{ $manual->title }}"
                        class="max-h-[500px] w-full object-cover">

                </div>

            @endif


            {{-- Description --}}
            @if ($manual->description)

                <div class="rounded-xl border border-app-border bg-white">

                    <div class="border-b border-app-border px-5 py-4">

                        <h2 class="text-base font-semibold text-secondary-dark">
                            Description
                        </h2>

                    </div>


                    <div class="p-5">

                        <p class="whitespace-pre-line text-sm leading-6 text-secondary-dark">
                            {{ $manual->description }}
                        </p>

                    </div>

                </div>

            @endif


            {{-- Content --}}
            @if ($manual->content)

                <div class="rounded-xl border border-app-border bg-white">

                    <div class="border-b border-app-border px-5 py-4">

                        <h2 class="text-base font-semibold text-secondary-dark">
                            Content
                        </h2>

                    </div>


                    <div class="p-5">

                        <div class="prose max-w-none text-sm text-secondary-dark">

                            {!! nl2br(e($manual->content)) !!}

                        </div>

                    </div>

                </div>

            @endif


            {{-- Uploaded Files --}}
            <div class="rounded-xl border border-app-border bg-white">

                <div class="border-b border-app-border px-5 py-4">

                    <div class="flex items-center justify-between">

                        <div>

                            <h2 class="text-base font-semibold text-secondary-dark">
                                Uploaded Resources
                            </h2>

                            <p class="mt-1 text-xs text-secondary">
                                Files uploaded for this sales manual.
                            </p>

                        </div>


                        <span class="rounded-full bg-primary-light px-3 py-1 text-xs font-medium text-primary">

                            {{ $manual->attachments->count() }}
                            {{ $manual->attachments->count() === 1 ? 'File' : 'Files' }}

                        </span>

                    </div>

                </div>


                @if ($manual->attachments->count())

                    <div class="divide-y divide-app-border">

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


                            <div class="flex items-center gap-4 p-5">


                                {{-- Icon --}}
                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $iconBg }}">

                                    <x-icon
                                        name="{{ $icon }}"
                                        class="h-6 w-6" />

                                </div>


                                {{-- File Details --}}
                                <div class="min-w-0 flex-1">

                                    <p
                                        class="truncate text-sm font-semibold text-secondary-dark"
                                        title="{{ $attachment->file_name }}">

                                        {{ $attachment->file_name }}

                                    </p>


                                    <div class="mt-1 flex flex-wrap gap-x-3 gap-y-1 text-xs text-secondary">

                                        <span class="uppercase">
                                            {{ $attachment->file_type }}
                                        </span>


                                        @if ($attachment->file_size)

                                            <span>
                                                {{ number_format($attachment->file_size / 1024 / 1024, 2) }}
                                                MB
                                            </span>

                                        @endif

                                    </div>

                                </div>


                                {{-- Actions --}}
                                <div class="flex shrink-0 items-center gap-2">

                                    <a
                                        href="{{ asset('storage/' . $attachment->file_path) }}"
                                        target="_blank"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-app-border bg-white px-3 py-2 text-sm font-medium text-secondary-dark transition hover:bg-surface-alt">

                                        <x-icon
                                            name="eye"
                                            class="h-4 w-4" />

                                        View

                                    </a>


                                    <a
                                        href="{{ asset('storage/' . $attachment->file_path) }}"
                                        download="{{ $attachment->file_name }}"
                                        class="inline-flex items-center justify-center rounded-lg bg-primary px-3 py-2 text-white transition hover:bg-primary/90"
                                        title="Download">

                                        <x-icon
                                            name="download"
                                            class="h-4 w-4" />

                                    </a>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="px-6 py-12 text-center">

                        <div
                            class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-surface-alt">

                            <x-icon
                                name="document"
                                class="h-6 w-6 text-secondary" />

                        </div>

                        <p class="mt-3 text-sm font-medium text-secondary-dark">
                            No files uploaded
                        </p>

                        <p class="mt-1 text-xs text-secondary">
                            This manual does not have any uploaded resources.
                        </p>

                    </div>

                @endif

            </div>

        </div>


        {{-- Right Sidebar --}}
        <div class="space-y-6">


            {{-- Manual Information --}}
            <div class="rounded-xl border border-app-border bg-white">

                <div class="border-b border-app-border px-5 py-4">

                    <h2 class="text-base font-semibold text-secondary-dark">
                        Manual Information
                    </h2>

                </div>


                <div class="divide-y divide-app-border">

                    <div class="flex items-center justify-between px-5 py-4">

                        <span class="text-sm text-secondary">
                            Type
                        </span>

                        <span class="text-sm font-medium text-secondary-dark">
                            {{ ucwords(str_replace('_', ' ', $manual->type)) }}
                        </span>

                    </div>


                    <div class="flex items-center justify-between px-5 py-4">

                        <span class="text-sm text-secondary">
                            Category
                        </span>

                        <span class="text-sm font-medium text-secondary-dark">
                            {{ $manual->category ?: '—' }}
                        </span>

                    </div>


                    <div class="flex items-center justify-between px-5 py-4">

                        <span class="text-sm text-secondary">
                            Sort Order
                        </span>

                        <span class="text-sm font-medium text-secondary-dark">
                            {{ $manual->sort_order }}
                        </span>

                    </div>


                    <div class="flex items-center justify-between px-5 py-4">

                        <span class="text-sm text-secondary">
                            Created
                        </span>

                        <span class="text-sm font-medium text-secondary-dark">
                            {{ $manual->created_at?->format('d M Y, h:i A') }}
                        </span>

                    </div>


                    @if ($manual->published_at)

                        <div class="flex items-center justify-between px-5 py-4">

                            <span class="text-sm text-secondary">
                                Published
                            </span>

                            <span class="text-sm font-medium text-secondary-dark">
                                {{ $manual->published_at->format('d M Y, h:i A') }}
                            </span>

                        </div>

                    @endif

                </div>

            </div>


            {{-- Quick Actions --}}
            <div class="rounded-xl border border-app-border bg-white">

                <div class="border-b border-app-border px-5 py-4">

                    <h2 class="text-base font-semibold text-secondary-dark">
                        Quick Actions
                    </h2>

                </div>


                <div class="space-y-2 p-4">

                    <a
                        href="{{ route('user.manuals') }}"
                        class="flex w-full items-center gap-3 rounded-lg border border-app-border px-4 py-3 text-sm font-medium text-secondary-dark transition hover:bg-surface-alt">

                        <x-icon
                            name="arrow-left"
                            class="h-4 w-4" />

                        Back to All Manuals

                    </a>

                </div>

            </div>

        </div>

    </div>

</x-layout>