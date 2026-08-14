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
        background:#101827;
        color:#fff;
        font-family:Arial,Helvetica,sans-serif;
    "
>

    {{-- =========================================================
         GOLD FRAME
    ========================================================== --}}

    <div
        class="absolute inset-[2%] border-2"
        style="border-color:#d4af37;"
    ></div>

    <div
        class="absolute inset-[3%] border"
        style="border-color:rgba(212,175,55,.35);"
    ></div>


    {{-- =========================================================
         DECORATIVE CIRCLES
    ========================================================== --}}

    <div
        class="absolute -left-[8%] -top-[20%] h-[60%] w-[30%] rounded-full"
        style="background:rgba(212,175,55,.08);"
    ></div>

    <div
        class="absolute -bottom-[20%] -right-[5%] h-[60%] w-[30%] rounded-full"
        style="background:rgba(212,175,55,.08);"
    ></div>


    {{-- =========================================================
         COMPANY NAME
    ========================================================== --}}

    @if ($companyName)

        <div
            class="absolute left-1/2 top-[5%] w-[70%] -translate-x-1/2 text-center"
        >

            <div
                class="font-bold uppercase tracking-[0.15em]"
                style="
                    color:#d4af37;
                    font-size:clamp(13px,1.4vw,23px);
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
        class="absolute left-1/2 top-[12%] w-full -translate-x-1/2 text-center"
    >

        <div
            class="uppercase tracking-[0.3em]"
            style="
                color:#d4af37;
                font-size:clamp(13px,1.3vw,22px);
            "
        >
            Achievement
        </div>


        <div
            class="mt-3 font-light uppercase tracking-[0.15em]"
            style="
                color:#ffffff;
                font-size:clamp(30px,4vw,60px);
                line-height:1.1;
            "
        >
            Certificate
        </div>

    </div>


    {{-- =========================================================
         NAME
    ========================================================== --}}

    <div
        class="absolute left-1/2 top-[39%] w-[80%] -translate-x-1/2 text-center"
    >

        <div
            class="font-semibold"
            style="
                color:#ffffff;
                font-size:clamp(32px,4.5vw,70px);
                line-height:1.1;
            "
        >
            {{ $certificate->user->name }}
        </div>


        <div
            class="mx-auto mt-4 h-px w-[35%]"
            style="background:#d4af37;"
        ></div>

    </div>


    {{-- =========================================================
         COURSE
    ========================================================== --}}

    <div
        class="absolute left-1/2 top-[53%] w-[75%] -translate-x-1/2 text-center"
    >

        <div
            style="
                color:#cbd5e1;
                font-size:clamp(13px,1.3vw,21px);
            "
        >
            has successfully completed
        </div>


        <div
            class="mt-3 font-semibold"
            style="
                color:#d4af37;
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
            class="absolute left-1/2 bottom-[16%] w-[82%] -translate-x-1/2 text-center"
        >

            {{-- Address --}}

            @if ($companyAddress)

                <div
                    style="
                        color:#94a3b8;
                        font-size:clamp(9px,.85vw,14px);
                        line-height:1.4;
                    "
                >
                    {{ $companyAddress }}
                </div>

            @endif


            {{-- Contact information --}}

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
        class="absolute bottom-[7%] left-[10%] right-[10%] flex items-end justify-between gap-6"
    >

        {{-- =====================================================
             DATE
        ====================================================== --}}

        <div class="w-[22%] min-w-[150px] text-center">

            <div
                class="border-b pb-2"
                style="
                    min-width:180px;
                    border-color:#64748b;
                    color:#ffffff;
                    font-size:clamp(10px,1vw,16px);
                "
            >
                {{ $certificate->issued_at->format('d M Y') }}
            </div>


            <div
                class="mt-2 uppercase tracking-widest"
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
                    class="mx-auto mb-2 h-auto max-h-[55px] max-w-[180px] object-contain"
                >

            @else

                <div class="h-[55px]"></div>

            @endif


            <div
                class="border-t px-8 pt-2"
                style="border-color:#64748b;"
            >

                <div
                    class="font-semibold"
                    style="
                        color:#ffffff;
                        font-size:clamp(11px,1vw,16px);
                    "
                >
                    {{ $template?->signer_name ?: 'Authorized Signatory' }}
                </div>


                @if ($template?->signer_designation)

                    <div
                        class="mt-1"
                        style="
                            color:#94a3b8;
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
                class="border-b pb-2"
                style="
                    min-width:180px;
                    border-color:#64748b;
                    color:#ffffff;
                    font-size:clamp(10px,1vw,16px);
                "
            >
                {{ $certificate->certificate_number }}
            </div>


            <div
                class="mt-2 uppercase tracking-widest"
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
                color:#64748b;
                font-size:clamp(8px,.75vw,12px);
            "
        >
            {{ $companyName }}
        </div>

    @endif

</div>