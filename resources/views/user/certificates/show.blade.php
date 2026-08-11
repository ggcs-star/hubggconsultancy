@php
    $fields = $certificate->course->certificateFields();
@endphp

<x-layout title="Certificate — {{ $certificate->course->title }}">
    <div class="print:hidden">
        <nav class="flex items-center gap-2 text-sm text-secondary">
            <a href="{{ route('user.certificates.index') }}" class="hover:text-primary">Certificates</a>
            <x-icon name="chevron-right" class="w-3.5 h-3.5" />
            <span class="font-medium text-secondary-dark">{{ $certificate->course->title }}</span>
        </nav>

        <div class="mt-3 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-secondary-dark">Certificate of Completion</h1>
                <p class="text-sm text-secondary">Issued {{ $certificate->issued_at->format('d M Y') }} &middot; {{ $certificate->certificate_number }}</p>
            </div>
            <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-dark">
                <x-icon name="download" class="w-4 h-4" />
                Print / Save as PDF
            </button>
        </div>
    </div>

    <div class="mt-6 flex justify-center print:mt-0">
        <div class="relative inline-block max-w-full overflow-hidden rounded-xl border border-app-border bg-white shadow-sm print:border-0 print:shadow-none">
            <img src="{{ $certificate->course->certificateBackgroundUrl() }}" alt="" class="block max-w-full">

            <span class="absolute -translate-x-1/2 -translate-y-1/2 whitespace-nowrap font-semibold"
                style="top: {{ $fields['name']['top'] }}%; left: {{ $fields['name']['left'] }}%; font-size: {{ $fields['name']['font_size'] }}px; color: {{ $fields['name']['color'] }};">
                {{ $certificate->user->name }}
            </span>
            <span class="absolute -translate-x-1/2 -translate-y-1/2 whitespace-nowrap font-semibold"
                style="top: {{ $fields['course']['top'] }}%; left: {{ $fields['course']['left'] }}%; font-size: {{ $fields['course']['font_size'] }}px; color: {{ $fields['course']['color'] }};">
                {{ $certificate->course->title }}
            </span>
            <span class="absolute -translate-x-1/2 -translate-y-1/2 whitespace-nowrap"
                style="top: {{ $fields['date']['top'] }}%; left: {{ $fields['date']['left'] }}%; font-size: {{ $fields['date']['font_size'] }}px; color: {{ $fields['date']['color'] }};">
                {{ $certificate->issued_at->format('d M Y') }}
            </span>
            <span class="absolute -translate-x-1/2 -translate-y-1/2 whitespace-nowrap"
                style="top: {{ $fields['certificate_id']['top'] }}%; left: {{ $fields['certificate_id']['left'] }}%; font-size: {{ $fields['certificate_id']['font_size'] }}px; color: {{ $fields['certificate_id']['color'] }};">
                {{ $certificate->certificate_number }}
            </span>
        </div>
    </div>
</x-layout>
