<x-layout title="{{ $manual->title }}">

    {{-- Header --}}
    <div>

        <a
            href="{{ route('user.manuals') }}"
            class="inline-flex items-center gap-2 text-sm text-secondary hover:text-secondary-dark">

            <x-icon name="arrow-left" class="h-4 w-4" />

            Back to Sales Manuals

        </a>

        <div class="mt-5">

            <div class="flex flex-wrap items-center gap-2">

                @php
                    $typeLabels = [
                        'manual' => 'Manual',
                        'guide' => 'Sales Guide',
                        'cheat_sheet' => 'Cheat Sheet',
                        'faq' => 'FAQ',
                        'sop' => 'SOP',
                        'script' => 'Sales Script',
                    ];
                @endphp

                <span class="rounded-full bg-primary-light px-2.5 py-1 text-xs font-semibold text-primary">
                    {{ $typeLabels[$manual->type] ?? ucfirst($manual->type) }}
                </span>

                @if ($manual->category)

                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-secondary">
                        {{ $manual->category }}
                    </span>

                @endif

                @if ($manual->is_featured)

                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                        Featured
                    </span>

                @endif

            </div>


            <h1 class="mt-3 text-2xl font-semibold text-secondary-dark">
                {{ $manual->title }}
            </h1>


            @if ($manual->description)

                <p class="mt-2 max-w-3xl text-sm leading-6 text-secondary">
                    {{ $manual->description }}
                </p>

            @endif

        </div>

    </div>


    {{-- Cover Image --}}
    @if ($manual->cover_image)

        <div class="mt-6 overflow-hidden rounded-xl border border-app-border bg-white">

            <img
                src="{{ asset('storage/' . $manual->cover_image) }}"
                alt="{{ $manual->title }}"
                class="max-h-[450px] w-full object-cover">

        </div>

    @endif


    {{-- Main Area --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">


        {{-- Guidance Content --}}
        <div class="lg:col-span-2">

            <div class="rounded-xl border border-app-border bg-white">

                <div class="border-b border-app-border px-6 py-4">

                    <h2 class="text-base font-semibold text-secondary-dark">
                        Sales Guidance
                    </h2>

                </div>


                <div class="p-6">

                    @if ($manual->content)

                        <div class="whitespace-pre-wrap text-sm leading-7 text-secondary-dark">
                            {{ $manual->content }}
                        </div>

                    @else

                        <div class="py-10 text-center">

                            <x-icon
                                name="document"
                                class="mx-auto h-8 w-8 text-secondary" />

                            <p class="mt-3 text-sm text-secondary">
                                No additional guidance available.
                            </p>

                        </div>

                    @endif

                </div>

            </div>

        </div>


        {{-- Resources --}}
        <div>

            <div class="rounded-xl border border-app-border bg-white">

                <div class="border-b border-app-border px-5 py-4">

                    <div class="flex items-center justify-between">

                        <h2 class="text-base font-semibold text-secondary-dark">
                            Resources
                        </h2>

                        <span class="text-xs text-secondary">
                            {{ $manual->attachments->count() }}
                        </span>

                    </div>

                </div>


                @if ($manual->attachments->count())

                    <div class="divide-y divide-app-border">

                        @foreach ($manual->attachments as $attachment)

                            <a
                                href="{{ asset('storage/' . $attachment->file_path) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center gap-3 px-5 py-4 transition hover:bg-surface-alt">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light">

                                    <x-icon
                                        name="paper-clip"
                                        class="h-5 w-5 text-primary" />

                                </div>


                                <div class="min-w-0 flex-1">

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


                                <x-icon
                                    name="download"
                                    class="h-4 w-4 shrink-0 text-secondary" />

                            </a>

                        @endforeach

                    </div>

                @else

                    <div class="px-5 py-8 text-center">

                        <x-icon
                            name="paper-clip"
                            class="mx-auto h-7 w-7 text-secondary" />

                        <p class="mt-3 text-sm text-secondary">
                            No downloadable resources.
                        </p>

                    </div>

                @endif

            </div>


            {{-- Back Button --}}
            <a
                href="{{ route('user.manuals') }}"
                class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg border border-app-border bg-white px-4 py-2.5 text-sm font-medium text-secondary-dark transition hover:bg-surface-alt">

                <x-icon name="arrow-left" class="h-4 w-4" />

                Back to Sales Manuals

            </a>

        </div>

    </div>

</x-layout>