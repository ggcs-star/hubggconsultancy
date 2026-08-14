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
         BORDER
    ========================================================== --}}

    <div
        class="absolute inset-[3%] border"
        style="border-color:#1e293b;"
    ></div>


    {{-- =========================================================
         COMPANY NAME
    ========================================================== --}}

    @if ($companyName)

        <div
            class="absolute right-[8%] top-[7%] text-right"
        >

            <div
                class="font-semibold uppercase tracking-[0.12em]"
                style="
                    color:#0f172a;
                    font-size:clamp(12px,1.2vw,20px);
                "
            >
                {{ $companyName }}
            </div>

        </div>

    @endif


    {{-- =========================================================
         TOP ACCENT
    ========================================================== --}}

    <div
        class="absolute left-[7%] top-[8%] h-3 w-[20%]"
        style="background:#1e293b;"
    ></div>


    {{-- =========================================================
         HEADING
    ========================================================== --}}

    <div
        class="absolute left-[8%] top-[18%]"
    >

        <div
            class="uppercase tracking-[0.25em]"
            style="
                color:#64748b;
                font-size:clamp(12px,1.2vw,20px);
            "
        >
            Certificate
        </div>


        <div
            class="mt-2 font-semibold"
            style="
                color:#0f172a;
                font-size:clamp(30px,4vw,58px);
                line-height:1.1;
            "
        >
            Of Completion
        </div>

    </div>


    {{-- =========================================================
         STUDENT + COURSE
    ========================================================== --}}

    <div
        class="absolute left-[8%] right-[8%] top-[40%]"
    >

        <p
            style="
                color:#64748b;
                font-size:clamp(13px,1.3vw,21px);
                margin:0;
            "
        >
            Presented to
        </p>


        <div
            class="mt-2 font-semibold"
            style="
                color:#0f172a;
                font-size:clamp(32px,4vw,62px);
                line-height:1.1;
            "
        >
            {{ $certificate->user->name }}
        </div>


        <div
            class="mt-5"
            style="
                color:#64748b;
                font-size:clamp(13px,1.3vw,21px);
            "
        >
            for successfully completing
        </div>


        <div
            class="mt-2 font-medium"
            style="
                color:#334155;
                font-size:clamp(22px,2.3vw,38px);
                line-height:1.15;
            "
        >
            {{ $certificate->course->title }}
        </div>

    </div>


    {{-- =========================================================
         COMPANY CONTACT DETAILS
    ========================================================== --}}

    @if (
        $companyAddress ||
        $companyPhone ||
        $companyEmail ||
        $companyWebsite
    )

        <div
            class="absolute left-[8%] bottom-[16%] w-[70%]"
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


            {{-- Contact --}}

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
         FOOTER
    ========================================================== --}}

    <div
        class="absolute bottom-[7%] left-[8%] right-[8%] flex items-end justify-between gap-6"
    >

        {{-- =====================================================
             DATE
        ====================================================== --}}

        <div class="w-[22%] min-w-[150px]">

            <div
                style="
                    color:#0f172a;
                    font-size:clamp(11px,1vw,16px);
                "
            >
                {{ $certificate->issued_at->format('d M Y') }}
            </div>


            <div
                class="mt-1 uppercase tracking-wider"
                style="
                    color:#94a3b8;
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
                    class="mx-auto mb-2 h-auto max-h-[50px] max-w-[170px] object-contain"
                >

            @else

                <div class="h-[50px]"></div>

            @endif


            <div
                class="border-t px-8 pt-2"
                style="border-color:#cbd5e1;"
            >

                <div
                    class="font-medium"
                    style="
                        color:#0f172a;
                        font-size:clamp(10px,1vw,15px);
                    "
                >
                    {{ $template?->signer_name ?: 'Authorized Signatory' }}
                </div>


                @if ($template?->signer_designation)

                    <div
                        class="mt-1"
                        style="
                            color:#64748b;
                            font-size:clamp(8px,.8vw,12px);
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
                style="
                    color:#0f172a;
                    font-size:clamp(10px,1vw,15px);
                "
            >
                {{ $certificate->certificate_number }}
            </div>


            <div
                class="mt-1 uppercase tracking-wider"
                style="
                    color:#94a3b8;
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
            class="absolute bottom-[2.3%] left-1/2 w-[80%] -translate-x-1/2 text-center"
            style="
                color:#94a3b8;
                font-size:clamp(8px,.75vw,12px);
            "
        >
            {{ $companyName }}
        </div>

    @endif

</div>