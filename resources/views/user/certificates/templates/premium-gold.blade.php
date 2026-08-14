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
    class="certificate-template relative mx-auto w-full overflow-hidden"
    style="
        aspect-ratio:1600/1100;
        max-width:1600px;
        min-height:650px;
        background:#fffdf7;
        font-family:Georgia,'Times New Roman',serif;
    "
>

    {{-- =========================================================
         GOLD FRAME
    ========================================================== --}}

    <div
        class="absolute inset-[1.5%] border-[10px]"
        style="border-color:#b88a24;"
    ></div>

    <div
        class="absolute inset-[2.4%] border"
        style="border-color:#e2c46f;"
    ></div>


    {{-- =========================================================
         DECORATIVE CORNERS
    ========================================================== --}}

    <div
        class="absolute left-[4%] top-[6%] h-20 w-20 rounded-full border-2"
        style="border-color:#b88a24;"
    ></div>

    <div
        class="absolute right-[4%] top-[6%] h-20 w-20 rounded-full border-2"
        style="border-color:#b88a24;"
    ></div>

    <div
        class="absolute bottom-[6%] left-[4%] h-20 w-20 rounded-full border-2"
        style="border-color:#b88a24;"
    ></div>

    <div
        class="absolute bottom-[6%] right-[4%] h-20 w-20 rounded-full border-2"
        style="border-color:#b88a24;"
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
                    color:#80611b;
                    font-size:clamp(12px,1.25vw,20px);
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
        class="absolute left-1/2 top-[11%] w-full -translate-x-1/2 text-center"
    >

        <div
            class="uppercase tracking-[0.2em]"
            style="
                color:#80611b;
                font-size:clamp(14px,1.4vw,24px);
            "
        >
            Achievement Award
        </div>


        <div
            class="mt-3 font-bold"
            style="
                color:#4b3a12;
                font-size:clamp(30px,4vw,60px);
                line-height:1.1;
            "
        >
            Certificate of Completion
        </div>

    </div>


    {{-- =========================================================
         NAME
    ========================================================== --}}

    <div
        class="absolute left-1/2 top-[37%] w-[80%] -translate-x-1/2 text-center"
    >

        <p
            style="
                color:#80611b;
                font-size:clamp(14px,1.4vw,22px);
                margin:0;
            "
        >
            This certificate is awarded to
        </p>


        <div
            class="mt-3 font-semibold italic"
            style="
                color:#3f3215;
                font-size:clamp(32px,4.2vw,68px);
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
        class="absolute left-1/2 top-[53%] w-[75%] -translate-x-1/2 text-center"
    >

        <p
            style="
                color:#80611b;
                font-size:clamp(13px,1.3vw,21px);
                margin:0;
            "
        >
            has successfully completed
        </p>


        <div
            class="mt-3 font-semibold"
            style="
                color:#4b3a12;
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
            class="absolute left-1/2 bottom-[16%] w-[78%] -translate-x-1/2 text-center"
        >

            {{-- Address --}}

            @if ($companyAddress)

                <div
                    style="
                        color:#80611b;
                        font-size:clamp(9px,.85vw,14px);
                        line-height:1.4;
                    "
                >
                    {{ $companyAddress }}
                </div>

            @endif


            {{-- Contact Details --}}

            @if (
                $companyPhone ||
                $companyEmail ||
                $companyWebsite
            )

                <div
                    class="mt-1"
                    style="
                        color:#a58b52;
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
        class="absolute bottom-[7%] left-[12%] right-[12%] flex items-end justify-between gap-6"
    >

        {{-- =====================================================
             DATE
        ====================================================== --}}

        <div class="w-[22%] min-w-[150px] text-center">

            <div
                class="border-b px-8 pb-2"
                style="
                    border-color:#b88a24;
                    color:#4b3a12;
                    font-size:clamp(11px,1vw,17px);
                "
            >
                {{ $certificate->issued_at->format('d M Y') }}
            </div>


            <div
                class="mt-2 uppercase tracking-widest"
                style="
                    color:#80611b;
                    font-size:clamp(8px,.8vw,12px);
                "
            >
                Issued Date
            </div>

        </div>


        {{-- =====================================================
             SIGNATURE
        ====================================================== --}}

        <div class="w-[28%] min-w-[200px] text-center">

            @if ($template?->signatureUrl())

                <img
                    src="{{ $template->signatureUrl() }}"
                    class="mx-auto mb-2 h-auto max-h-[55px] max-w-[180px] object-contain"
                    alt="Authorized Signature"
                >

            @else

                <div class="h-[55px]"></div>

            @endif


            <div
                class="border-t px-8 pt-2"
                style="border-color:#b88a24;"
            >

                <div
                    class="font-semibold"
                    style="
                        color:#4b3a12;
                        font-size:clamp(11px,1vw,16px);
                    "
                >
                    {{ $template?->signer_name ?: 'Authorized Signatory' }}
                </div>


                @if ($template?->signer_designation)

                    <div
                        class="mt-1"
                        style="
                            color:#80611b;
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

        <div class="w-[22%] min-w-[150px] text-center">

            <div
                class="border-b px-8 pb-2"
                style="
                    border-color:#b88a24;
                    color:#4b3a12;
                    font-size:clamp(10px,1vw,16px);
                "
            >
                {{ $certificate->certificate_number }}
            </div>


            <div
                class="mt-2 uppercase tracking-widest"
                style="
                    color:#80611b;
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
            class="absolute bottom-[2.5%] left-1/2 w-[80%] -translate-x-1/2 text-center"
            style="
                color:#80611b;
                font-size:clamp(9px,.8vw,13px);
            "
        >
            {{ $companyName }}
        </div>

    @endif

</div>