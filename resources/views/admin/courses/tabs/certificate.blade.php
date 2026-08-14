@php
    $selectedTemplateId = (int) ($course->certificate_template_id ?? 0);
@endphp

<div
    x-data="certificateTemplateSelector({
        selected: {{ $selectedTemplateId }},
    })"
    class="space-y-6"
>

    {{-- ============================================================
         HEADER
    ============================================================ --}}

    <div class="rounded-xl border border-app-border bg-white p-6">

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-light">
                        <x-icon
                            name="certificate"
                            class="h-5 w-5 text-primary"
                        />
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-secondary-dark">
                            Certificate Template
                        </h2>

                        <p class="mt-1 text-sm text-secondary">
                            Select the certificate design for
                            <span class="font-medium text-secondary-dark">
                                {{ $course->title }}
                            </span>.
                        </p>
                    </div>

                </div>
            </div>


            {{-- Current selection --}}

            <div
                class="inline-flex items-center gap-2 rounded-full bg-success-light px-3 py-1.5 text-xs font-semibold text-success"
                x-show="selected > 0"
            >
                <span class="h-2 w-2 rounded-full bg-success"></span>

                <span>
                    Selected:
                </span>

                <span x-text="selectedName"></span>
            </div>

        </div>

    </div>


    {{-- ============================================================
         SUCCESS
    ============================================================ --}}

    @if (session('success'))

        <div class="rounded-lg border border-success/20 bg-success-light px-4 py-3 text-sm text-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ============================================================
         ERRORS
    ============================================================ --}}

    @if ($errors->any())

        <div class="rounded-lg border border-danger/20 bg-danger-light px-4 py-3 text-sm text-danger">

            <ul class="list-disc space-y-1 pl-5">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ============================================================
         FORM
    ============================================================ --}}

    <form
        method="POST"
        action="{{ route('admin.courses.certificate.update', $course) }}"
    >

        @csrf
        @method('PUT')

        <input
            type="hidden"
            name="certificate_template_id"
            x-model="selected"
        >


        {{-- ========================================================
             AVAILABLE DESIGNS
        ======================================================== --}}

        <div class="rounded-xl border border-app-border bg-white p-6">

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wide text-secondary">
                    Available Certificate Designs
                </h3>

                <p class="mt-1 text-xs text-secondary">
                    Choose one design. The selected design will automatically
                    be used when the trainee completes this course.
                </p>
            </div>


            {{-- ====================================================
                 TEMPLATE CARDS
            ==================================================== --}}

            <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">

                @forelse ($templates as $template)

                    @php
                        $design = $template->design_type ?: $template->slug;
                    @endphp

                    <button
                        type="button"
                        @click="
                            selected = {{ $template->id }};
                            selectedName = @js($template->name);
                        "
                        class="group relative block w-full text-left"
                    >

                        <div
                            class="
                                relative overflow-hidden rounded-2xl border-2
                                bg-white transition-all duration-200
                                hover:-translate-y-1 hover:shadow-xl
                            "
                            :class="
                                selected === {{ $template->id }}
                                    ? 'border-primary ring-4 ring-primary/10 shadow-lg'
                                    : 'border-app-border'
                            "
                        >

                            {{-- SELECTED --}}
                            <div
                                x-show="selected === {{ $template->id }}"
                                class="absolute right-3 top-3 z-20"
                            >
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-white shadow-lg">

                                    <x-icon
                                        name="check"
                                        class="h-5 w-5"
                                    />

                                </div>
                            </div>


                            {{-- =================================================
                                 CERTIFICATE PREVIEW
                            ================================================== --}}

                            <div class="relative aspect-[16/10] overflow-hidden">

                                @if ($template->previewUrl())

                                    <img
                                        src="{{ $template->previewUrl() }}"
                                        alt="{{ $template->name }}"
                                        class="h-full w-full object-cover"
                                    >

                                @else

                                    {{-- ========================================
                                         CLASSIC BLUE
                                    ========================================= --}}

                                    @if ($design === 'classic-blue')

                                        <div class="relative h-full overflow-hidden bg-white">

                                            <div class="absolute inset-y-0 left-0 w-[7%] bg-[#173f73]"></div>

                                            <div class="absolute left-[7%] top-0 h-full w-[12%] rounded-r-full bg-[#edf3f8]"></div>

                                            <div class="absolute right-0 top-0 h-full w-[1.8%] bg-[#f5b841]"></div>

                                            <div class="relative z-10 flex h-full flex-col items-center px-10 pt-8">

                                                <p class="text-[8px] font-bold tracking-[0.45em] text-[#173f73]">
                                                    GLOBAL GARNER
                                                </p>

                                                <h3 class="mt-2 text-2xl font-bold tracking-[0.18em] text-[#173f73]">
                                                    CERTIFICATE
                                                </h3>

                                                <p class="text-[9px] tracking-[0.35em] text-[#64748b]">
                                                    OF COMPLETION
                                                </p>

                                                <p class="mt-5 text-[9px] text-[#64748b]">
                                                    This certificate is presented to
                                                </p>

                                                <p class="mt-1 text-xl font-bold text-[#173f73]">
                                                    Piyush Rajput
                                                </p>

                                                <p class="mt-2 text-[8px] text-[#64748b]">
                                                    for successfully completing
                                                </p>

                                                <p class="mt-1 text-sm font-semibold text-[#173f73]">
                                                    {{ $course->title }}
                                                </p>

                                                <div class="mt-auto mb-6 flex w-full justify-between px-10 text-[7px] text-[#475569]">
                                                    <span>14 Aug 2026</span>
                                                    <span>Authorized Signature</span>
                                                </div>

                                            </div>

                                        </div>


                                    {{-- =================================================
                                         PREMIUM GOLD
                                    ================================================== --}}

                                    @elseif ($design === 'premium-gold')

                                        <div class="relative h-full overflow-hidden bg-[#fffdf7]">

                                            <div class="absolute inset-3 border border-[#d4af37]"></div>

                                            <div class="absolute left-0 top-0 h-14 w-14 rounded-br-full bg-[#d4af37]"></div>

                                            <div class="absolute bottom-0 right-0 h-14 w-14 rounded-tl-full bg-[#d4af37]"></div>

                                            <div class="relative z-10 flex h-full flex-col items-center px-10 pt-8">

                                                <p class="text-[8px] font-bold tracking-[0.5em] text-[#9a7610]">
                                                    GLOBAL GARNER
                                                </p>

                                                <h3 class="mt-3 font-serif text-2xl font-semibold text-[#6f5510]">
                                                    Certificate of Excellence
                                                </h3>

                                                <div class="mt-3 h-px w-24 bg-[#d4af37]"></div>

                                                <p class="mt-5 text-[9px] text-[#6b7280]">
                                                    Proudly presented to
                                                </p>

                                                <p class="mt-1 font-serif text-xl font-bold text-[#5d4810]">
                                                    Piyush Rajput
                                                </p>

                                                <p class="mt-2 text-[8px] text-[#6b7280]">
                                                    for successfully completing
                                                </p>

                                                <p class="text-sm font-semibold text-[#7b5f12]">
                                                    {{ $course->title }}
                                                </p>

                                                <div class="mt-auto mb-5 flex w-full justify-between px-12 text-[7px]">
                                                    <span class="text-[#6b7280]">14 Aug 2026</span>
                                                    <span class="text-[#6b7280]">Director</span>
                                                </div>

                                            </div>

                                        </div>


                                    {{-- =================================================
                                         MODERN GREEN
                                    ================================================== --}}

                                    @elseif ($design === 'modern-green')

                                        <div class="relative h-full overflow-hidden bg-white">

                                            <div class="absolute inset-y-0 left-0 w-[14%] bg-[#117a65]"></div>

                                            <div class="absolute right-0 inset-y-0 w-[2%] bg-[#9bdc28]"></div>

                                            <div class="absolute left-[7%] top-[-25%] h-[80%] w-[18%] rounded-full bg-[#e8f2ef]"></div>

                                            <div class="relative z-10 ml-[14%] flex h-full flex-col px-12 pt-9">

                                                <h3 class="text-2xl font-bold tracking-[0.2em] text-[#117a65]">
                                                    CERTIFICATE
                                                </h3>

                                                <p class="mt-1 text-[9px] tracking-[0.35em] text-[#64748b]">
                                                    OF COMPLETION
                                                </p>

                                                <p class="mt-5 text-[9px] text-[#64748b]">
                                                    This certificate is presented to
                                                </p>

                                                <p class="mt-1 text-xl font-bold text-[#117a65]">
                                                    Piyush Rajput
                                                </p>

                                                <p class="mt-2 text-[8px] text-[#64748b]">
                                                    for successfully completing
                                                </p>

                                                <p class="text-sm font-semibold text-[#117a65]">
                                                    {{ $course->title }}
                                                </p>

                                                <div class="mt-auto mb-6 flex justify-between text-[7px] text-[#64748b]">
                                                    <span>14 Aug 2026</span>
                                                    <span>Global Garner</span>
                                                </div>

                                            </div>

                                        </div>


                                    {{-- =================================================
                                         DARK PREMIUM
                                    ================================================== --}}

                                    @elseif ($design === 'dark-premium')

                                        <div class="relative h-full overflow-hidden bg-[#111827]">

                                            <div class="absolute inset-2 border border-[#d4af37]"></div>

                                            <div class="absolute left-0 top-0 h-20 w-20 rounded-br-full bg-[#d4af37]"></div>

                                            <div class="absolute bottom-0 right-0 h-20 w-20 rounded-tl-full bg-[#d4af37]"></div>

                                            <div class="relative z-10 flex h-full flex-col items-center px-10 pt-8 text-center">

                                                <p class="text-[8px] font-bold tracking-[0.5em] text-[#f1d477]">
                                                    GLOBAL GARNER
                                                </p>

                                                <h3 class="mt-3 text-2xl font-bold tracking-[0.18em] text-white">
                                                    CERTIFICATE
                                                </h3>

                                                <p class="text-[8px] tracking-[0.4em] text-[#cbd5e1]">
                                                    OF COMPLETION
                                                </p>

                                                <p class="mt-5 text-[9px] text-[#cbd5e1]">
                                                    Presented to
                                                </p>

                                                <p class="mt-1 text-xl font-bold text-[#f1d477]">
                                                    Piyush Rajput
                                                </p>

                                                <p class="mt-2 text-[8px] text-[#cbd5e1]">
                                                    for successfully completing
                                                </p>

                                                <p class="text-sm font-semibold text-white">
                                                    {{ $course->title }}
                                                </p>

                                                <div class="mt-auto mb-5 flex w-full justify-between px-12 text-[7px] text-[#cbd5e1]">
                                                    <span>14 Aug 2026</span>
                                                    <span>Authorized Signatory</span>
                                                </div>

                                            </div>

                                        </div>


                                    {{-- =================================================
                                         MINIMAL WHITE
                                    ================================================== --}}

                                    @elseif ($design === 'minimal-white')

                                        <div class="relative h-full overflow-hidden bg-white">

                                            <div class="absolute left-0 top-0 h-full w-2 bg-black"></div>

                                            <div class="absolute right-0 top-0 h-full w-2 bg-black"></div>

                                            <div class="relative z-10 flex h-full flex-col items-center px-12 pt-10">

                                                <p class="text-[8px] font-semibold tracking-[0.4em] text-gray-500">
                                                    GLOBAL GARNER
                                                </p>

                                                <h3 class="mt-4 text-2xl font-light tracking-[0.15em] text-gray-900">
                                                    CERTIFICATE
                                                </h3>

                                                <p class="mt-1 text-[8px] tracking-[0.3em] text-gray-500">
                                                    OF COMPLETION
                                                </p>

                                                <div class="mt-5 h-px w-20 bg-gray-300"></div>

                                                <p class="mt-5 text-[9px] text-gray-500">
                                                    Presented to
                                                </p>

                                                <p class="mt-1 text-xl font-semibold text-gray-900">
                                                    Piyush Rajput
                                                </p>

                                                <p class="mt-2 text-[8px] text-gray-500">
                                                    {{ $course->title }}
                                                </p>

                                                <div class="mt-auto mb-5 text-[7px] text-gray-500">
                                                    14 Aug 2026
                                                </div>

                                            </div>

                                        </div>


                                    {{-- =================================================
                                         ACADEMIC
                                    ================================================== --}}

                                    @elseif ($design === 'academic')

                                        <div class="relative h-full overflow-hidden bg-[#f8f6ef]">

                                            <div class="absolute inset-3 border-2 border-[#263b5b]"></div>

                                            <div class="absolute inset-5 border border-[#c5a55b]"></div>

                                            <div class="relative z-10 flex h-full flex-col items-center px-12 pt-8">

                                                <p class="text-[8px] font-bold tracking-[0.4em] text-[#263b5b]">
                                                    GLOBAL GARNER
                                                </p>

                                                <h3 class="mt-3 font-serif text-2xl font-bold text-[#263b5b]">
                                                    Certificate of Completion
                                                </h3>

                                                <div class="mt-2 h-px w-28 bg-[#c5a55b]"></div>

                                                <p class="mt-5 text-[9px] text-[#475569]">
                                                    This certificate is awarded to
                                                </p>

                                                <p class="mt-1 font-serif text-xl font-bold text-[#263b5b]">
                                                    Piyush Rajput
                                                </p>

                                                <p class="mt-2 text-[8px] text-[#475569]">
                                                    for completing
                                                </p>

                                                <p class="text-sm font-semibold text-[#263b5b]">
                                                    {{ $course->title }}
                                                </p>

                                                <div class="mt-auto mb-5 flex w-full justify-between px-14 text-[7px] text-[#475569]">
                                                    <span>14 Aug 2026</span>
                                                    <span>Director</span>
                                                </div>

                                            </div>

                                        </div>


                                    {{-- =================================================
                                         MODERN GRADIENT
                                    ================================================== --}}

                                    @else

                                        <div class="relative h-full overflow-hidden bg-gradient-to-br from-[#4f46e5] via-[#7c3aed] to-[#ec4899]">

                                            <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-white/10"></div>

                                            <div class="absolute -bottom-16 -left-10 h-48 w-48 rounded-full bg-white/10"></div>

                                            <div class="relative z-10 flex h-full flex-col items-center px-10 pt-8 text-center text-white">

                                                <p class="text-[8px] font-bold tracking-[0.5em] text-white/80">
                                                    GLOBAL GARNER
                                                </p>

                                                <h3 class="mt-3 text-2xl font-bold tracking-[0.15em]">
                                                    CERTIFICATE
                                                </h3>

                                                <p class="text-[8px] tracking-[0.4em] text-white/70">
                                                    OF COMPLETION
                                                </p>

                                                <p class="mt-5 text-[9px] text-white/80">
                                                    Presented to
                                                </p>

                                                <p class="mt-1 text-xl font-bold">
                                                    Piyush Rajput
                                                </p>

                                                <p class="mt-2 text-[8px] text-white/80">
                                                    for successfully completing
                                                </p>

                                                <p class="text-sm font-semibold">
                                                    {{ $course->title }}
                                                </p>

                                                <div class="mt-auto mb-5 flex w-full justify-between px-10 text-[7px] text-white/80">
                                                    <span>14 Aug 2026</span>
                                                    <span>Authorized Signatory</span>
                                                </div>

                                            </div>

                                        </div>

                                    @endif

                                @endif

                            </div>


                            {{-- =================================================
                                 CARD INFO
                            ================================================== --}}

                            <div class="p-4">

                                <div class="flex items-start justify-between gap-3">

                                    <div>

                                        <h4 class="text-sm font-semibold text-secondary-dark">
                                            {{ $template->name }}
                                        </h4>

                                        <p class="mt-1 text-xs text-secondary">
                                            {{ ucwords(str_replace('-', ' ', $template->design_type)) }}
                                        </p>

                                    </div>


                                    <span
                                        x-show="selected === {{ $template->id }}"
                                        class="rounded-full bg-primary-light px-2.5 py-1 text-[11px] font-semibold text-primary"
                                    >
                                        Selected
                                    </span>

                                </div>


                                <div class="mt-3 flex items-center gap-1.5 text-xs text-secondary">

                                    <x-icon
                                        name="layers"
                                        class="h-3.5 w-3.5"
                                    />

                                    Used by

                                    <span class="font-semibold text-secondary-dark">
                                        {{ $template->courses_count }}
                                    </span>

                                    course{{ $template->courses_count == 1 ? '' : 's' }}

                                </div>

                            </div>

                        </div>

                    </button>

                @empty

                    <div class="col-span-full rounded-xl border border-dashed border-app-border p-10 text-center">

                        <x-icon
                            name="file"
                            class="mx-auto h-10 w-10 text-secondary"
                        />

                        <h3 class="mt-3 text-sm font-semibold text-secondary-dark">
                            No certificate templates available
                        </h3>

                        <p class="mt-1 text-xs text-secondary">
                            Create certificate templates first.
                        </p>

                    </div>

                @endforelse

            </div>

        </div>


        {{-- ============================================================
             LIVE PREVIEW
        ============================================================ --}}

        <div class="mt-6 rounded-xl border border-app-border bg-white p-6">

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <h3 class="text-sm font-semibold uppercase tracking-wide text-secondary">
                        Live Preview
                    </h3>

                    <p class="mt-1 text-xs text-secondary">
                        This preview changes immediately when you select another template.
                    </p>

                </div>

                <div class="rounded-full bg-primary-light px-3 py-1.5 text-xs font-semibold text-primary">
                    <span x-text="selectedName || 'No template selected'"></span>
                </div>

            </div>


            {{-- ========================================================
                 LIVE PREVIEW CONTAINER
            ======================================================== --}}

            <div class="mt-6 rounded-xl bg-surface-alt p-4 sm:p-8">

                @foreach ($templates as $template)

                    @php
                        $design = $template->design_type ?: $template->slug;
                    @endphp

                    <div
                        x-show="selected === {{ $template->id }}"
                        x-cloak
                        class="mx-auto w-full max-w-5xl"
                    >

                        {{-- =================================================
                             CLASSIC BLUE
                        ================================================== --}}

                        @if ($design === 'classic-blue')

                            <div class="relative aspect-[16/10] overflow-hidden rounded-lg bg-white shadow-xl">

                                <div class="absolute inset-y-0 left-0 w-[7%] bg-[#173f73]"></div>

                                <div class="absolute left-[7%] top-0 h-full w-[12%] rounded-r-full bg-[#edf3f8]"></div>

                                <div class="absolute right-0 inset-y-0 w-[1.5%] bg-[#f5b841]"></div>

                                <div class="relative z-10 flex h-full flex-col items-center pt-[7%]">

                                    <p class="text-[clamp(8px,1vw,16px)] font-bold tracking-[0.45em] text-[#173f73]">
                                        GLOBAL GARNER
                                    </p>

                                    <h2 class="mt-2 text-[clamp(24px,4vw,64px)] font-bold tracking-[0.15em] text-[#173f73]">
                                        CERTIFICATE
                                    </h2>

                                    <p class="text-[clamp(8px,1vw,15px)] tracking-[0.35em] text-[#64748b]">
                                        OF COMPLETION
                                    </p>

                                    <p class="mt-[5%] text-[clamp(9px,1.1vw,18px)] text-[#64748b]">
                                        This certificate is presented to
                                    </p>

                                    <p class="mt-1 text-[clamp(22px,3.5vw,58px)] font-bold text-[#173f73]">
                                        {{ $course->assignedUsers->first()?->name ?? 'Piyush Rajput' }}
                                    </p>

                                    <p class="mt-2 text-[clamp(8px,1vw,16px)] text-[#64748b]">
                                        for successfully completing
                                    </p>

                                    <p class="text-[clamp(12px,1.5vw,24px)] font-semibold text-[#173f73]">
                                        {{ $course->title }}
                                    </p>

                                    <div class="absolute bottom-[7%] left-[12%] right-[8%] flex justify-between text-[clamp(7px,.8vw,13px)] text-[#475569]">
                                        <span>{{ now()->format('d M Y') }}</span>
                                        <span>Authorized Signatory</span>
                                    </div>

                                </div>

                            </div>


                        {{-- =================================================
                             PREMIUM GOLD
                        ================================================== --}}

                        @elseif ($design === 'premium-gold')

                            <div class="relative aspect-[16/10] overflow-hidden rounded-lg bg-[#fffdf7] shadow-xl">

                                <div class="absolute inset-4 border-2 border-[#d4af37]"></div>

                                <div class="relative z-10 flex h-full flex-col items-center pt-[8%]">

                                    <p class="text-[clamp(8px,1vw,15px)] font-bold tracking-[0.5em] text-[#9a7610]">
                                        GLOBAL GARNER
                                    </p>

                                    <h2 class="mt-4 font-serif text-[clamp(24px,3.5vw,58px)] font-semibold text-[#6f5510]">
                                        Certificate of Excellence
                                    </h2>

                                    <div class="mt-3 h-px w-[15%] bg-[#d4af37]"></div>

                                    <p class="mt-[5%] text-[clamp(9px,1vw,16px)] text-[#6b7280]">
                                        Proudly presented to
                                    </p>

                                    <p class="mt-1 font-serif text-[clamp(22px,3.2vw,52px)] font-bold text-[#5d4810]">
                                        Piyush Rajput
                                    </p>

                                    <p class="mt-2 text-[clamp(8px,1vw,15px)] text-[#6b7280]">
                                        for successfully completing
                                    </p>

                                    <p class="text-[clamp(12px,1.4vw,23px)] font-semibold text-[#7b5f12]">
                                        {{ $course->title }}
                                    </p>

                                    <div class="absolute bottom-[7%] left-[12%] right-[12%] flex justify-between text-[clamp(7px,.8vw,13px)] text-[#6b7280]">
                                        <span>{{ now()->format('d M Y') }}</span>
                                        <span>Director</span>
                                    </div>

                                </div>

                            </div>


                        {{-- =================================================
                             MODERN GREEN
                        ================================================== --}}

                        @elseif ($design === 'modern-green')

                            <div class="relative aspect-[16/10] overflow-hidden rounded-lg bg-white shadow-xl">

                                <div class="absolute inset-y-0 left-0 w-[14%] bg-[#117a65]"></div>

                                <div class="absolute right-0 inset-y-0 w-[2%] bg-[#9bdc28]"></div>

                                <div class="absolute left-[7%] top-[-20%] h-[80%] w-[18%] rounded-full bg-[#e8f2ef]"></div>

                                <div class="relative z-10 ml-[14%] flex h-full flex-col px-[7%] pt-[9%]">

                                    <h2 class="text-[clamp(24px,3.8vw,62px)] font-bold tracking-[0.15em] text-[#117a65]">
                                        CERTIFICATE
                                    </h2>

                                    <p class="text-[clamp(8px,1vw,15px)] tracking-[0.35em] text-[#64748b]">
                                        OF COMPLETION
                                    </p>

                                    <p class="mt-[5%] text-[clamp(9px,1vw,16px)] text-[#64748b]">
                                        This certificate is presented to
                                    </p>

                                    <p class="mt-1 text-[clamp(22px,3.2vw,52px)] font-bold text-[#117a65]">
                                        Piyush Rajput
                                    </p>

                                    <p class="mt-2 text-[clamp(8px,1vw,15px)] text-[#64748b]">
                                        for successfully completing
                                    </p>

                                    <p class="text-[clamp(12px,1.4vw,23px)] font-semibold text-[#117a65]">
                                        {{ $course->title }}
                                    </p>

                                    <div class="absolute bottom-[7%] left-[20%] right-[8%] flex justify-between text-[clamp(7px,.8vw,13px)] text-[#64748b]">
                                        <span>{{ now()->format('d M Y') }}</span>
                                        <span>Global Garner</span>
                                    </div>

                                </div>

                            </div>


                        {{-- =================================================
                             DARK PREMIUM
                        ================================================== --}}

                        @elseif ($design === 'dark-premium')

                            <div class="relative aspect-[16/10] overflow-hidden rounded-lg bg-[#111827] shadow-xl">

                                <div class="absolute inset-3 border border-[#d4af37]"></div>

                                <div class="relative z-10 flex h-full flex-col items-center pt-[8%] text-center">

                                    <p class="text-[clamp(8px,1vw,15px)] font-bold tracking-[0.5em] text-[#f1d477]">
                                        GLOBAL GARNER
                                    </p>

                                    <h2 class="mt-3 text-[clamp(24px,3.5vw,58px)] font-bold tracking-[0.15em] text-white">
                                        CERTIFICATE
                                    </h2>

                                    <p class="text-[clamp(8px,1vw,15px)] tracking-[0.4em] text-[#cbd5e1]">
                                        OF COMPLETION
                                    </p>

                                    <p class="mt-[5%] text-[clamp(9px,1vw,16px)] text-[#cbd5e1]">
                                        Presented to
                                    </p>

                                    <p class="mt-1 text-[clamp(22px,3.2vw,52px)] font-bold text-[#f1d477]">
                                        Piyush Rajput
                                    </p>

                                    <p class="mt-2 text-[clamp(8px,1vw,15px)] text-[#cbd5e1]">
                                        for successfully completing
                                    </p>

                                    <p class="text-[clamp(12px,1.4vw,23px)] font-semibold text-white">
                                        {{ $course->title }}
                                    </p>

                                    <div class="absolute bottom-[7%] left-[10%] right-[10%] flex justify-between text-[clamp(7px,.8vw,13px)] text-[#cbd5e1]">
                                        <span>{{ now()->format('d M Y') }}</span>
                                        <span>Authorized Signatory</span>
                                    </div>

                                </div>

                            </div>


                        {{-- =================================================
                             MINIMAL WHITE
                        ================================================== --}}

                        @elseif ($design === 'minimal-white')

                            <div class="relative aspect-[16/10] overflow-hidden rounded-lg bg-white shadow-xl">

                                <div class="absolute left-0 top-0 h-full w-2 bg-black"></div>

                                <div class="absolute right-0 top-0 h-full w-2 bg-black"></div>

                                <div class="relative z-10 flex h-full flex-col items-center pt-[10%]">

                                    <p class="text-[clamp(8px,1vw,15px)] font-semibold tracking-[0.4em] text-gray-500">
                                        GLOBAL GARNER
                                    </p>

                                    <h2 class="mt-5 text-[clamp(24px,3.5vw,58px)] font-light tracking-[0.15em] text-gray-900">
                                        CERTIFICATE
                                    </h2>

                                    <p class="text-[clamp(8px,1vw,15px)] tracking-[0.3em] text-gray-500">
                                        OF COMPLETION
                                    </p>

                                    <div class="mt-5 h-px w-[12%] bg-gray-300"></div>

                                    <p class="mt-[5%] text-[clamp(9px,1vw,16px)] text-gray-500">
                                        Presented to
                                    </p>

                                    <p class="mt-1 text-[clamp(22px,3.2vw,52px)] font-semibold text-gray-900">
                                        Piyush Rajput
                                    </p>

                                    <p class="mt-2 text-[clamp(10px,1vw,16px)] text-gray-500">
                                        {{ $course->title }}
                                    </p>

                                    <div class="absolute bottom-[8%] text-[clamp(7px,.8vw,13px)] text-gray-500">
                                        {{ now()->format('d M Y') }}
                                    </div>

                                </div>

                            </div>


                        {{-- =================================================
                             ACADEMIC
                        ================================================== --}}

                        @elseif ($design === 'academic')

                            <div class="relative aspect-[16/10] overflow-hidden rounded-lg bg-[#f8f6ef] shadow-xl">

                                <div class="absolute inset-[2%] border-2 border-[#263b5b]"></div>

                                <div class="absolute inset-[3.5%] border border-[#c5a55b]"></div>

                                <div class="relative z-10 flex h-full flex-col items-center pt-[8%]">

                                    <p class="text-[clamp(8px,1vw,15px)] font-bold tracking-[0.4em] text-[#263b5b]">
                                        GLOBAL GARNER
                                    </p>

                                    <h2 class="mt-4 font-serif text-[clamp(24px,3.5vw,58px)] font-bold text-[#263b5b]">
                                        Certificate of Completion
                                    </h2>

                                    <div class="mt-2 h-px w-[15%] bg-[#c5a55b]"></div>

                                    <p class="mt-[5%] text-[clamp(9px,1vw,16px)] text-[#475569]">
                                        This certificate is awarded to
                                    </p>

                                    <p class="mt-1 font-serif text-[clamp(22px,3.2vw,52px)] font-bold text-[#263b5b]">
                                        Piyush Rajput
                                    </p>

                                    <p class="mt-2 text-[clamp(8px,1vw,15px)] text-[#475569]">
                                        for completing
                                    </p>

                                    <p class="text-[clamp(12px,1.4vw,23px)] font-semibold text-[#263b5b]">
                                        {{ $course->title }}
                                    </p>

                                    <div class="absolute bottom-[7%] left-[12%] right-[12%] flex justify-between text-[clamp(7px,.8vw,13px)] text-[#475569]">
                                        <span>{{ now()->format('d M Y') }}</span>
                                        <span>Director</span>
                                    </div>

                                </div>

                            </div>


                        {{-- =================================================
                             MODERN GRADIENT
                        ================================================== --}}

                        @else

                            <div class="relative aspect-[16/10] overflow-hidden rounded-lg bg-gradient-to-br from-[#4f46e5] via-[#7c3aed] to-[#ec4899] shadow-xl">

                                <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/10"></div>

                                <div class="absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-white/10"></div>

                                <div class="relative z-10 flex h-full flex-col items-center pt-[8%] text-center text-white">

                                    <p class="text-[clamp(8px,1vw,15px)] font-bold tracking-[0.5em] text-white/80">
                                        GLOBAL GARNER
                                    </p>

                                    <h2 class="mt-3 text-[clamp(24px,3.5vw,58px)] font-bold tracking-[0.15em]">
                                        CERTIFICATE
                                    </h2>

                                    <p class="text-[clamp(8px,1vw,15px)] tracking-[0.4em] text-white/70">
                                        OF COMPLETION
                                    </p>

                                    <p class="mt-[5%] text-[clamp(9px,1vw,16px)] text-white/80">
                                        Presented to
                                    </p>

                                    <p class="mt-1 text-[clamp(22px,3.2vw,52px)] font-bold">
                                        Piyush Rajput
                                    </p>

                                    <p class="mt-2 text-[clamp(8px,1vw,15px)] text-white/80">
                                        for successfully completing
                                    </p>

                                    <p class="text-[clamp(12px,1.4vw,23px)] font-semibold">
                                        {{ $course->title }}
                                    </p>

                                    <div class="absolute bottom-[7%] left-[10%] right-[10%] flex justify-between text-[clamp(7px,.8vw,13px)] text-white/80">
                                        <span>{{ now()->format('d M Y') }}</span>
                                        <span>Authorized Signatory</span>
                                    </div>

                                </div>

                            </div>

                        @endif

                    </div>

                @endforeach


                {{-- No selection --}}

                <div
                    x-show="selected === 0"
                    x-cloak
                    class="flex min-h-[300px] items-center justify-center"
                >

                    <div class="text-center">

                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary-light">

                            <x-icon
                                name="certificate"
                                class="h-7 w-7 text-primary"
                            />

                        </div>

                        <h4 class="mt-4 text-sm font-semibold text-secondary-dark">
                            Select a certificate design
                        </h4>

                        <p class="mt-1 text-xs text-secondary">
                            Choose one of the templates above to see the live preview.
                        </p>

                    </div>

                </div>

            </div>

        </div>


        {{-- ============================================================
             CURRENT SETTINGS
        ============================================================ --}}

        <div class="rounded-xl border border-app-border bg-white p-6">

            <h3 class="text-sm font-semibold uppercase tracking-wide text-secondary">
                Course Certificate Settings
            </h3>

            <div class="mt-4 grid gap-4 md:grid-cols-3">

                <div class="rounded-lg bg-surface-alt p-4">

                    <p class="text-xs uppercase tracking-wide text-secondary">
                        Course
                    </p>

                    <p class="mt-1 text-sm font-semibold text-secondary-dark">
                        {{ $course->title }}
                    </p>

                </div>


                <div class="rounded-lg bg-surface-alt p-4">

                    <p class="text-xs uppercase tracking-wide text-secondary">
                        Selected Template
                    </p>

                    <p
                        class="mt-1 text-sm font-semibold text-secondary-dark"
                        x-text="selectedName || 'Not Selected'"
                    ></p>

                </div>


                <div class="rounded-lg bg-surface-alt p-4">

                    <p class="text-xs uppercase tracking-wide text-secondary">
                        Certificate
                    </p>

                    <p
                        class="mt-1 text-sm font-semibold"
                        :class="selected > 0 ? 'text-success' : 'text-danger'"
                        x-text="selected > 0 ? 'Ready — Dynamic' : 'Not Configured'"
                    ></p>

                </div>

            </div>

        </div>


        {{-- ============================================================
             SAVE
        ============================================================ --}}

        <div class="flex items-center justify-between">

            <p class="text-xs text-secondary">
                The selected template will be used automatically for this course.
            </p>

            <x-primary-button>

                <x-icon
                    name="check"
                    class="h-4 w-4"
                />

                Save Certificate Template

            </x-primary-button>

        </div>

    </form>


    {{-- ============================================================
         ALPINE
    ============================================================ --}}

    <script>

        function certificateTemplateSelector(config) {

            const templateNames = @js(
                $templates->pluck('name', 'id')
            );

            return {

                selected: Number(config.selected || 0),

                selectedName:
                    templateNames[Number(config.selected || 0)]
                    || '',

                init() {

                    this.$watch('selected', value => {

                        this.selectedName =
                            templateNames[Number(value)]
                            || '';

                    });

                }

            };

        }

    </script>


    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

</div>