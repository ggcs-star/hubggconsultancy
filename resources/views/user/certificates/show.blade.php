<x-layout title="Certificate — {{ $certificate->course->title }}">

    <div class="print:hidden">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-secondary">

            <a
                href="{{ route('user.certificates.index') }}"
                class="hover:text-primary"
            >
                Certificates
            </a>

            <x-icon
                name="chevron-right"
                class="h-3.5 w-3.5"
            />

            <span class="font-medium text-secondary-dark">
                {{ $certificate->course->title }}
            </span>

        </nav>


        {{-- Header --}}
        <div class="mt-3 flex flex-wrap items-center justify-between gap-4">

            <div>

                <h1 class="text-xl font-semibold text-secondary-dark">
                    Certificate of Completion
                </h1>

                <p class="text-sm text-secondary">
                    Issued
                    {{ $certificate->issued_at->format('d M Y') }}

                    &middot;

                    {{ $certificate->certificate_number }}
                </p>

            </div>


            {{-- Print / Save --}}
            <button
                type="button"
                onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary-dark"
            >

                <x-icon
                    name="download"
                    class="h-4 w-4"
                />

                Print / Save as PDF

            </button>

        </div>

    </div>


    {{-- ============================================================
         CERTIFICATE
    ============================================================ --}}

    <div class="mt-6 flex justify-center print:mt-0">

        @if ($template)

            @php
                $design = $template->design_type;
            @endphp


            {{-- ====================================================
                 CLASSIC BLUE
            ==================================================== --}}

            @if ($design === 'classic-blue')

                @include(
                    'user.certificates.templates.classic-blue',
                    [
                        'certificate' => $certificate,
                        'template' => $template,
                    ]
                )


            {{-- ====================================================
                 PREMIUM GOLD
            ==================================================== --}}

            @elseif ($design === 'premium-gold')

                @include(
                    'user.certificates.templates.premium-gold',
                    [
                        'certificate' => $certificate,
                        'template' => $template,
                    ]
                )


            {{-- ====================================================
                 MODERN GREEN
            ==================================================== --}}

            @elseif ($design === 'modern-green')

                @include(
                    'user.certificates.templates.modern-green',
                    [
                        'certificate' => $certificate,
                        'template' => $template,
                    ]
                )


            {{-- ====================================================
                 DARK PREMIUM
            ==================================================== --}}

            @elseif ($design === 'dark-premium')

                @include(
                    'user.certificates.templates.dark-premium',
                    [
                        'certificate' => $certificate,
                        'template' => $template,
                    ]
                )


            {{-- ====================================================
                 MINIMAL WHITE
            ==================================================== --}}

            @elseif ($design === 'minimal-white')

                @include(
                    'user.certificates.templates.minimal-white',
                    [
                        'certificate' => $certificate,
                        'template' => $template,
                    ]
                )


            {{-- ====================================================
                 ACADEMIC
            ==================================================== --}}

            @elseif ($design === 'academic')

                @include(
                    'user.certificates.templates.academic',
                    [
                        'certificate' => $certificate,
                        'template' => $template,
                    ]
                )


            {{-- ====================================================
                 MODERN GRADIENT
            ==================================================== --}}

            @elseif ($design === 'modern-gradient')

                @include(
                    'user.certificates.templates.modern-gradient',
                    [
                        'certificate' => $certificate,
                        'template' => $template,
                    ]
                )


            {{-- ====================================================
                 INVALID DESIGN
            ==================================================== --}}

            @else

                <div class="rounded-xl border border-danger/20 bg-white p-10 text-center">

                    <x-icon
                        name="alert-circle"
                        class="mx-auto h-10 w-10 text-danger"
                    />

                    <p class="mt-3 text-sm font-medium text-danger">
                        Certificate template design is not available.
                    </p>

                    <p class="mt-1 text-xs text-secondary">
                        Please contact the administrator.
                    </p>

                </div>

            @endif


        @else

            {{-- ====================================================
                 NO TEMPLATE ASSIGNED
            ==================================================== --}}

            <div class="rounded-xl border border-app-border bg-white p-10 text-center">

                <x-icon
                    name="file"
                    class="mx-auto h-10 w-10 text-secondary"
                />

                <p class="mt-3 text-sm font-medium text-secondary-dark">
                    Certificate template is not configured.
                </p>

                <p class="mt-1 text-sm text-secondary">
                    Please contact the administrator.
                </p>

            </div>

        @endif

    </div>


    {{-- ============================================================
         PRINT CSS
    ============================================================ --}}

    <style>
        @media print {

            @page {
                size: landscape;
                margin: 0;
            }

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }

            body * {
                visibility: hidden;
            }

            .print\:hidden {
                display: none !important;
            }

            .certificate-template {
                visibility: visible !important;
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .certificate-template * {
                visibility: visible !important;
            }
        }
    </style>

</x-layout>