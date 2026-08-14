@php
    $template = $template ?? $certificate->course?->certificateTemplate;

    /*
    |--------------------------------------------------------------------------
    | Company Details
    |--------------------------------------------------------------------------
    */

    $company = $company ?? (
        $template
            ? $template->companyDetails()
            : [
                'name' => '',
                'address' => '',
                'phone' => '',
                'email' => '',
                'website' => '',
            ]
    );

    $companyName = $company['name'] ?? '';
    $companyAddress = $company['address'] ?? '';
    $companyPhone = $company['phone'] ?? '';
    $companyEmail = $company['email'] ?? '';
    $companyWebsite = $company['website'] ?? '';
@endphp


<div
    class="certificate-template relative mx-auto w-full overflow-hidden bg-white"
    style="
        aspect-ratio: 1600 / 1100;
        max-width: 1600px;
        min-height: 650px;
        font-family: Georgia, 'Times New Roman', serif;
    "
>

    {{-- =========================================================
         OUTER NAVY BORDER
    ========================================================== --}}

    <div
        class="pointer-events-none absolute inset-[1.5%] border-[8px]"
        style="border-color:#173b6c;"
    ></div>


    {{-- =========================================================
         INNER GOLD BORDER
    ========================================================== --}}

    <div
        class="pointer-events-none absolute inset-[2.2%] border-2"
        style="border-color:#c9a227;"
    ></div>


    {{-- =========================================================
         COMPANY NAME
    ========================================================== --}}

    @if ($companyName)

        <div
            class="absolute left-1/2 top-[5%] w-[70%] -translate-x-1/2 text-center"
        >

            <div
                class="font-bold uppercase tracking-[0.12em]"
                style="
                    color:#173b6c;
                    font-size:clamp(14px,1.5vw,24px);
                "
            >
                {{ $companyName }}
            </div>

        </div>

    @endif


    {{-- =========================================================
         CERTIFICATE HEADING
    ========================================================== --}}

    <div
        class="absolute left-1/2 top-[10%] w-full -translate-x-1/2 text-center"
    >

        <div
            class="font-bold uppercase tracking-[0.25em]"
            style="
                color:#173b6c;
                font-size:clamp(28px,4vw,60px);
                line-height:1.1;
            "
        >
            Certificate
        </div>


        <div
            class="mt-2 uppercase tracking-[0.35em]"
            style="
                color:#c9a227;
                font-size:clamp(14px,1.5vw,25px);
            "
        >
            Of Completion
        </div>

    </div>


    {{-- =========================================================
         INTRO
    ========================================================== --}}

    <div
        class="absolute left-1/2 top-[29%] w-[75%] -translate-x-1/2 text-center"
    >

        <p
            style="
                color:#64748b;
                font-size:clamp(14px,1.5vw,24px);
                margin:0;
            "
        >
            This certificate is proudly presented to
        </p>

    </div>


    {{-- =========================================================
         USER NAME
    ========================================================== --}}

    <div
        class="absolute left-1/2 top-[40%] w-[80%] -translate-x-1/2 text-center"
    >

        <div
            class="font-semibold"
            style="
                color:#173b6c;
                font-size:clamp(30px,4vw,64px);
                line-height:1.1;
            "
        >
            {{ $certificate->user->name }}
        </div>


        <div
            class="mx-auto mt-3 h-[2px] w-[35%]"
            style="background:#c9a227;"
        ></div>

    </div>


    {{-- =========================================================
         COURSE
    ========================================================== --}}

    <div
        class="absolute left-1/2 top-[53%] w-[80%] -translate-x-1/2 text-center"
    >

        <p
            style="
                color:#64748b;
                font-size:clamp(14px,1.4vw,23px);
                margin:0;
            "
        >
            for successfully completing
        </p>


        <div
            class="mt-3 font-semibold"
            style="
                color:#173b6c;
                font-size:clamp(22px,2.5vw,40px);
                line-height:1.15;
            "
        >
            {{ $certificate->course->title }}
        </div>

    </div>


    {{-- =========================================================
         COMPANY CONTACT INFORMATION
    ========================================================== --}}

    @if (
        $companyAddress ||
        $companyPhone ||
        $companyEmail ||
        $companyWebsite
    )

        <div
            class="absolute left-1/2 bottom-[15%] w-[82%] -translate-x-1/2 text-center"
        >

            @if ($companyAddress)

                <div
                    style="
                        color:#64748b;
                        font-size:clamp(9px,0.85vw,14px);
                        line-height:1.4;
                    "
                >
                    {{ $companyAddress }}
                </div>

            @endif


            @if (
                $companyPhone ||
                $companyEmail ||
                $companyWebsite
            )

                <div
                    class="mt-1"
                    style="
                        color:#64748b;
                        font-size:clamp(8px,0.75vw,13px);
                        line-height:1.4;
                    "
                >

                    @if ($companyPhone)

                        <span>
                            {{ $companyPhone }}
                        </span>

                    @endif


                    @if (
                        $companyPhone &&
                        $companyEmail
                    )

                        <span class="mx-1">
                            |
                        </span>

                    @endif


                    @if ($companyEmail)

                        <span>
                            {{ $companyEmail }}
                        </span>

                    @endif


                    @if (
                        ($companyPhone || $companyEmail) &&
                        $companyWebsite
                    )

                        <span class="mx-1">
                            |
                        </span>

                    @endif


                    @if ($companyWebsite)

                        <span>
                            {{ $companyWebsite }}
                        </span>

                    @endif

                </div>

            @endif

        </div>

    @endif


    {{-- =========================================================
         BOTTOM SECTION
    ========================================================== --}}

    <div
        class="absolute bottom-[7%] left-[8%] right-[8%] flex items-end justify-between gap-6"
    >

        {{-- =====================================================
             DATE
        ====================================================== --}}

        <div class="w-[22%] min-w-[150px] text-center">

            <div
                class="border-b pb-2"
                style="
                    border-color:#94a3b8;
                    color:#173b6c;
                    font-size:clamp(12px,1.2vw,19px);
                "
            >
                {{ $certificate->issued_at->format('d M Y') }}
            </div>


            <div
                class="mt-2 uppercase tracking-wider"
                style="
                    color:#64748b;
                    font-size:clamp(9px,0.8vw,13px);
                "
            >
                Date of Issue
            </div>

        </div>


        {{-- =====================================================
             SIGNATURE
        ====================================================== --}}

        <div class="w-[28%] min-w-[200px] text-center">

            @if ($template?->signatureUrl())

                <img
                    src="{{ $template->signatureUrl() }}"
                    alt="Authorized Signature"
                    class="mx-auto mb-2 h-auto max-h-[65px] max-w-[190px] object-contain"
                >

            @else

                <div class="h-[65px]"></div>

            @endif


            <div
                class="border-t pt-2"
                style="border-color:#94a3b8;"
            >

                <div
                    class="font-semibold"
                    style="
                        color:#173b6c;
                        font-size:clamp(11px,1vw,16px);
                    "
                >
                    {{ $template?->signer_name ?: 'Authorized Signatory' }}
                </div>


                @if ($template?->signer_designation)

                    <div
                        class="mt-1"
                        style="
                            color:#64748b;
                            font-size:clamp(9px,0.8vw,13px);
                        "
                    >
                        {{ $template->signer_designation }}
                    </div>

                @endif

            </div>

        </div>


        {{-- =====================================================
             CERTIFICATE ID
        ====================================================== --}}

        <div class="w-[22%] min-w-[150px] text-center">

            <div
                class="border-b pb-2"
                style="
                    border-color:#94a3b8;
                    color:#173b6c;
                    font-size:clamp(10px,1vw,16px);
                "
            >
                {{ $certificate->certificate_number }}
            </div>


            <div
                class="mt-2 uppercase tracking-wider"
                style="
                    color:#64748b;
                    font-size:clamp(9px,0.8vw,13px);
                "
            >
                Certificate ID
            </div>

        </div>

    </div>


    {{-- =========================================================
         COMPANY NAME FOOTER
    ========================================================== --}}

    @if ($companyName)

        <div
            class="absolute bottom-[2.2%] left-1/2 w-[80%] -translate-x-1/2 text-center"
            style="
                color:#64748b;
                font-size:clamp(8px,0.75vw,12px);
            "
        >
            {{ $companyName }}
        </div>

    @endif

</div>