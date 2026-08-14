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
        aspect-ratio:1600/1100;
        max-width:1600px;
        min-height:650px;
        font-family:Arial,Helvetica,sans-serif;
    "
>

    {{-- =========================================================
         GREEN SIDE PANEL
    ========================================================== --}}

    <div
        class="absolute bottom-0 left-0 top-0 w-[9%]"
        style="background:#126b5b;"
    ></div>

    <div
        class="absolute bottom-0 right-0 top-0 w-[2%]"
        style="background:#9bcf3f;"
    ></div>


    {{-- =========================================================
         DECORATIVE CIRCLES
    ========================================================== --}}

    <div
        class="absolute -left-[5%] -top-[12%] h-[45%] w-[22%] rounded-full opacity-10"
        style="background:#126b5b;"
    ></div>

    <div
        class="absolute -bottom-[18%] right-[3%] h-[40%] w-[20%] rounded-full opacity-10"
        style="background:#9bcf3f;"
    ></div>


    {{-- =========================================================
         COMPANY NAME
    ========================================================== --}}

    @if ($companyName)

        <div
            class="absolute right-[10%] top-[7%] max-w-[35%] text-right"
        >

            <div
                class="font-bold uppercase tracking-[0.08em]"
                style="
                    color:#126b5b;
                    font-size:clamp(11px,1.15vw,19px);
                    line-height:1.2;
                "
            >
                {{ $companyName }}
            </div>

        </div>

    @endif


    {{-- =========================================================
         HEADING
    ========================================================== --}}

    <div
        class="absolute left-[15%] top-[12%]"
    >

        <div
            class="font-bold uppercase tracking-[0.15em]"
            style="
                color:#126b5b;
                font-size:clamp(30px,4vw,60px);
                line-height:1.1;
            "
        >
            Certificate
        </div>


        <div
            class="mt-1 uppercase tracking-[0.3em]"
            style="
                color:#6b7280;
                font-size:clamp(13px,1.3vw,22px);
            "
        >
            Of Completion
        </div>

    </div>


    {{-- =========================================================
         CONTENT
    ========================================================== --}}

    <div
        class="absolute left-[15%] right-[10%] top-[33%]"
    >

        <p
            style="
                color:#64748b;
                font-size:clamp(14px,1.4vw,22px);
                margin:0;
            "
        >
            This certificate is presented to
        </p>


        {{-- Student Name --}}

        <div
            class="mt-3 font-bold"
            style="
                color:#126b5b;
                font-size:clamp(32px,4.5vw,70px);
                line-height:1.1;
            "
        >
            {{ $certificate->user->name }}
        </div>


        {{-- Course Intro --}}

        <div
            class="mt-6"
            style="
                color:#64748b;
                font-size:clamp(14px,1.3vw,21px);
            "
        >
            for successfully completing
        </div>


        {{-- Course --}}

        <div
            class="mt-2 font-semibold"
            style="
                color:#1f2937;
                font-size:clamp(22px,2.5vw,40px);
                line-height:1.15;
            "
        >
            {{ $certificate->course->title }}
        </div>

    </div>


    {{-- =========================================================
         COMPANY DETAILS
    ========================================================== --}}

    @if (
        $companyAddress ||
        $companyPhone ||
        $companyEmail ||
        $companyWebsite
    )

        <div
            class="absolute left-[15%] bottom-[16%] right-[10%]"
        >

            {{-- Address --}}

            @if ($companyAddress)

                <div
                    style="
                        color:#64748b;
                        font-size:clamp(9px,.85vw,14px);
                        line-height:1.4;
                    "
                >
                    {{ $companyAddress }}
                </div>

            @endif


            {{-- Contact Information --}}

            @if (
                $companyPhone ||
                $companyEmail ||
                $companyWebsite
            )

                <div
                    class="mt-1"
                    style="
                        color:#94a3b8;
                        font-size:clamp(8px,.75vw,13px);
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
        class="absolute bottom-[7%] left-[15%] right-[10%] flex items-end justify-between gap-6"
    >

        {{-- =====================================================
             DATE
        ====================================================== --}}

        <div class="w-[22%] min-w-[150px]">

            <div
                class="border-b pb-2"
                style="
                    min-width:190px;
                    border-color:#9ca3af;
                    color:#126b5b;
                    font-size:clamp(11px,1vw,17px);
                "
            >
                {{ $certificate->issued_at->format('d M Y') }}
            </div>


            <div
                class="mt-2 uppercase tracking-wider"
                style="
                    color:#6b7280;
                    font-size:clamp(8px,.8vw,12px);
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
                    class="mx-auto mb-2 h-auto max-h-[55px] max-w-[180px] object-contain"
                >

            @else

                <div class="h-[55px]"></div>

            @endif


            <div
                class="border-t px-8 pt-2"
                style="border-color:#9ca3af;"
            >

                {{-- Signer Name --}}

                <div
                    class="font-semibold"
                    style="
                        color:#126b5b;
                        font-size:clamp(11px,1vw,16px);
                    "
                >
                    {{ $template?->signer_name ?: 'Authorized Signatory' }}
                </div>


                {{-- Designation --}}

                @if ($template?->signer_designation)

                    <div
                        class="mt-1"
                        style="
                            color:#64748b;
                            font-size:clamp(9px,.8vw,13px);
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

        <div class="w-[22%] min-w-[150px] text-right">

            <div
                class="border-b pb-2"
                style="
                    min-width:190px;
                    border-color:#9ca3af;
                    color:#126b5b;
                    font-size:clamp(10px,1vw,16px);
                "
            >
                {{ $certificate->certificate_number }}
            </div>


            <div
                class="mt-2 uppercase tracking-wider"
                style="
                    color:#6b7280;
                    font-size:clamp(8px,.8vw,12px);
                "
            >
                Certificate ID
            </div>

        </div>

    </div>


    {{-- =========================================================
         COMPANY FOOTER
    ========================================================== --}}

    @if ($companyName)

        <div
            class="absolute bottom-[2.3%] left-[15%] right-[10%] text-center"
            style="
                color:#94a3b8;
                font-size:clamp(8px,.75vw,12px);
            "
        >
            {{ $companyName }}
        </div>

    @endif

</div>