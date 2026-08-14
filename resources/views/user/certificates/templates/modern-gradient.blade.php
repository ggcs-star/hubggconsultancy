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
         GRADIENT BACKGROUND
    ========================================================== --}}

    <div
        class="absolute left-0 top-0 h-full w-[28%]"
        style="
            background:linear-gradient(
                150deg,
                #4f46e5,
                #7c3aed,
                #2563eb
            );
        "
    ></div>


    {{-- =========================================================
         WHITE ANGLED PANEL
    ========================================================== --}}

    <div
        class="absolute left-[20%] top-[-10%] h-[125%] w-[18%] rotate-[8deg] bg-white"
    ></div>


    {{-- =========================================================
         COMPANY NAME
    ========================================================== --}}

    @if ($companyName)

        <div
            class="absolute left-[6%] top-[8%] w-[20%] text-center"
        >

            <div
                class="font-bold uppercase tracking-[0.08em]"
                style="
                    color:#ffffff;
                    font-size:clamp(11px,1.15vw,19px);
                    line-height:1.2;
                "
            >
                {{ $companyName }}
            </div>

        </div>

    @endif


    {{-- =========================================================
         SMALL ACCENT
    ========================================================== --}}

    <div
        class="absolute right-[6%] top-[8%] h-5 w-[15%] rounded-full"
        style="
            background:linear-gradient(
                90deg,
                #4f46e5,
                #7c3aed
            );
        "
    ></div>


    {{-- =========================================================
         HEADING
    ========================================================== --}}

    <div
        class="absolute left-[34%] right-[7%] top-[14%]"
    >

        <div
            class="uppercase tracking-[0.2em]"
            style="
                color:#6366f1;
                font-size:clamp(13px,1.3vw,21px);
            "
        >
            Achievement
        </div>


        <div
            class="mt-2 font-bold"
            style="
                color:#111827;
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
        class="absolute left-[34%] right-[7%] top-[37%]"
    >

        <div
            style="
                color:#64748b;
                font-size:clamp(13px,1.3vw,21px);
            "
        >
            Presented to
        </div>


        <div
            class="mt-2 font-bold"
            style="
                background:linear-gradient(
                    90deg,
                    #4f46e5,
                    #7c3aed
                );
                -webkit-background-clip:text;
                background-clip:text;
                color:transparent;
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
        class="absolute left-[34%] right-[7%] top-[54%]"
    >

        <div
            style="
                color:#64748b;
                font-size:clamp(13px,1.3vw,21px);
            "
        >
            Successfully completed
        </div>


        <div
            class="mt-2 font-semibold"
            style="
                color:#111827;
                font-size:clamp(22px,2.4vw,38px);
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
            class="absolute left-[34%] right-[7%] bottom-[16%]"
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


            {{-- Contact Row --}}

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
        class="absolute bottom-[7%] left-[34%] right-[7%] flex items-end justify-between gap-8"
    >

        {{-- =====================================================
             DATE
        ====================================================== --}}

        <div class="w-[22%] min-w-[150px]">

            <div
                class="border-b pb-2"
                style="
                    min-width:170px;
                    border-color:#cbd5e1;
                    color:#111827;
                    font-size:clamp(10px,1vw,16px);
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
                    class="font-semibold"
                    style="
                        color:#111827;
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
                class="border-b pb-2"
                style="
                    min-width:170px;
                    border-color:#cbd5e1;
                    color:#111827;
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
            class="absolute bottom-[2.3%] left-[34%] right-[7%] text-center"
            style="
                color:#94a3b8;
                font-size:clamp(8px,.75vw,12px);
            "
        >
            {{ $companyName }}
        </div>

    @endif

</div>