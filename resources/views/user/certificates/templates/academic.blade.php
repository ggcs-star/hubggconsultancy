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
    class="certificate-template relative mx-auto w-full overflow-hidden bg-[#faf9f3]"
    style="
        aspect-ratio:1600/1100;
        max-width:1600px;
        min-height:650px;
        font-family:Georgia,'Times New Roman',serif;
    "
>

    {{-- =========================================================
         ACADEMIC BORDER
    ========================================================== --}}

    <div
        class="absolute inset-[2%] border-[7px]"
        style="border-color:#5b4636;"
    ></div>

    <div
        class="absolute inset-[2.8%] border"
        style="border-color:#b99b6b;"
    ></div>


    {{-- =========================================================
         COMPANY NAME
    ========================================================== --}}

    @if ($companyName)

        <div
            class="absolute left-1/2 top-[4.5%] w-[70%] -translate-x-1/2 text-center"
        >

            <div
                class="font-bold uppercase tracking-[0.12em]"
                style="
                    color:#5b4636;
                    font-size:clamp(13px,1.4vw,22px);
                "
            >
                {{ $companyName }}
            </div>

        </div>

    @endif


    {{-- =========================================================
         ACADEMIC EMBLEM
    ========================================================== --}}

    <div
        class="absolute left-1/2 top-[8%] flex h-20 w-20 -translate-x-1/2 items-center justify-center rounded-full border-4"
        style="
            border-color:#5b4636;
            color:#5b4636;
        "
    >

        <span
            class="font-bold"
            style="font-size:24px;"
        >
            A
        </span>

    </div>


    {{-- =========================================================
         HEADING
    ========================================================== --}}

    <div
        class="absolute left-1/2 top-[22%] w-full -translate-x-1/2 text-center"
    >

        <div
            class="uppercase tracking-[0.25em]"
            style="
                color:#5b4636;
                font-size:clamp(14px,1.4vw,23px);
            "
        >
            Academic Achievement
        </div>


        <div
            class="mt-2 font-bold"
            style="
                color:#3f3024;
                font-size:clamp(30px,4vw,58px);
                line-height:1.1;
            "
        >
            Certificate of Completion
        </div>

    </div>


    {{-- =========================================================
         STUDENT NAME
    ========================================================== --}}

    <div
        class="absolute left-1/2 top-[40%] w-[80%] -translate-x-1/2 text-center"
    >

        <p
            style="
                color:#806b58;
                font-size:clamp(13px,1.3vw,21px);
                margin:0;
            "
        >
            This is to certify that
        </p>


        <div
            class="mt-3 font-bold"
            style="
                color:#3f3024;
                font-size:clamp(32px,4vw,64px);
                line-height:1.1;
            "
        >
            {{ $certificate->user->name }}
        </div>

    </div>


    {{-- =========================================================
         COURSE
    ========================================================== --}}

    <div
        class="absolute left-1/2 top-[55%] w-[75%] -translate-x-1/2 text-center"
    >

        <p
            style="
                color:#806b58;
                font-size:clamp(13px,1.3vw,21px);
                margin:0;
            "
        >
            has successfully completed the course
        </p>


        <div
            class="mt-3 font-semibold"
            style="
                color:#5b4636;
                font-size:clamp(21px,2.3vw,37px);
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
            class="absolute left-1/2 bottom-[15%] w-[82%] -translate-x-1/2 text-center"
        >

            {{-- Address --}}

            @if ($companyAddress)

                <div
                    style="
                        color:#806b58;
                        font-size:clamp(9px,0.85vw,14px);
                        line-height:1.4;
                    "
                >
                    {{ $companyAddress }}
                </div>

            @endif


            {{-- Contact Row --}}

            @if (
                $companyPhone ||
                $companyEmail ||
                $companyWebsite
            )

                <div
                    class="mt-1"
                    style="
                        color:#806b58;
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
                class="border-b px-8 pb-2"
                style="
                    border-color:#b99b6b;
                    color:#5b4636;
                    font-size:clamp(10px,1vw,16px);
                "
            >
                {{ $certificate->issued_at->format('d M Y') }}
            </div>


            <div
                class="mt-1 uppercase tracking-wider"
                style="
                    color:#806b58;
                    font-size:clamp(8px,.8vw,12px);
                "
            >
                Date
            </div>

        </div>


        {{-- =====================================================
             SIGNATURE
        ====================================================== --}}

        <div class="w-[28%] min-w-[200px] text-center">

            @if ($template?->signatureUrl())

                <img
                    src="{{ $template->signatureUrl() }}"
                    alt="Signature"
                    class="mx-auto mb-2 h-auto max-h-[55px] max-w-[180px] object-contain"
                >

            @else

                <div class="h-[55px]"></div>

            @endif


            <div
                class="border-t px-8 pt-2"
                style="border-color:#b99b6b;"
            >

                {{-- Signer Name --}}

                <div
                    class="font-semibold"
                    style="
                        color:#3f3024;
                        font-size:clamp(10px,1vw,15px);
                    "
                >
                    {{ $template?->signer_name ?: 'Authorized Signatory' }}
                </div>


                {{-- Designation --}}

                @if ($template?->signer_designation)

                    <div
                        class="mt-1"
                        style="
                            color:#806b58;
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

        <div class="w-[22%] min-w-[150px] text-center">

            <div
                class="border-b px-8 pb-2"
                style="
                    border-color:#b99b6b;
                    color:#5b4636;
                    font-size:clamp(10px,1vw,15px);
                "
            >
                {{ $certificate->certificate_number }}
            </div>


            <div
                class="mt-1 uppercase tracking-wider"
                style="
                    color:#806b58;
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
                color:#806b58;
                font-size:clamp(8px,.75vw,12px);
            "
        >
            {{ $companyName }}
        </div>

    @endif

</div>