@php
    $fieldLabels = [
        'name' => 'Recipient Name',
        'course' => 'Course Title',
        'date' => 'Completion Date',
        'certificate_id' => 'Certificate ID',
    ];
@endphp

<div x-data="{
        fields: {{ \Illuminate\Support\Js::from($course->certificateFields()) }},
        backgroundPreviewUrl: @js($course->certificateBackgroundUrl()),
        onBackgroundChange(event) {
            const file = event.target.files[0];
            if (file) this.backgroundPreviewUrl = URL.createObjectURL(file);
        },
    }">
    <form method="POST" action="{{ route('admin.courses.certificate.update', $course) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-xl border border-app-border bg-white p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">Background Image</h2>
            <p class="mt-1 text-xs text-secondary">Upload any certificate design — a blank template, or one already carrying your own branding. Leave this blank to keep the current image.</p>

            <input type="file" name="certificate_background" accept="image/*" x-on:change="onBackgroundChange($event)"
                class="mt-3 w-full rounded-lg border-app-border text-sm shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-surface-alt file:px-3 file:py-1.5 file:text-sm" />
            <x-input-error :messages="$errors->get('certificate_background')" class="mt-2" />

            @unless ($course->hasCertificateTemplate())
                <p class="mt-2 text-xs italic text-secondary">No certificate has been set up for this course yet — trainees won't see one until you upload a background here.</p>
            @endunless
        </div>

        <div class="rounded-xl border border-app-border bg-white p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">Live Preview</h2>
            <p class="mt-1 text-xs text-secondary">Sample values shown below — adjust position, size and color under "Field Positions" and watch it update here.</p>

            <div class="mt-4 flex justify-center rounded-lg bg-surface-alt p-4">
                <div class="relative inline-block max-w-full overflow-hidden rounded-lg border border-app-border bg-white">
                    <template x-if="backgroundPreviewUrl">
                        <img :src="backgroundPreviewUrl" class="block max-w-full">
                    </template>
                    <template x-if="! backgroundPreviewUrl">
                        <div class="flex h-64 w-[28rem] max-w-full items-center justify-center px-6 text-center text-sm text-secondary">
                            Upload a background image above to see a live preview
                        </div>
                    </template>

                    <span class="absolute -translate-x-1/2 -translate-y-1/2 whitespace-nowrap font-semibold"
                        :style="`top: ${fields.name.top}%; left: ${fields.name.left}%; font-size: ${fields.name.font_size}px; color: ${fields.name.color};`">
                        Your Name
                    </span>
                    <span class="absolute -translate-x-1/2 -translate-y-1/2 whitespace-nowrap font-semibold"
                        :style="`top: ${fields.course.top}%; left: ${fields.course.left}%; font-size: ${fields.course.font_size}px; color: ${fields.course.color};`">
                        {{ $course->title }}
                    </span>
                    <span class="absolute -translate-x-1/2 -translate-y-1/2 whitespace-nowrap"
                        :style="`top: ${fields.date.top}%; left: ${fields.date.left}%; font-size: ${fields.date.font_size}px; color: ${fields.date.color};`">
                        {{ now()->format('d M Y') }}
                    </span>
                    <span class="absolute -translate-x-1/2 -translate-y-1/2 whitespace-nowrap"
                        :style="`top: ${fields.certificate_id.top}%; left: ${fields.certificate_id.left}%; font-size: ${fields.certificate_id.font_size}px; color: ${fields.certificate_id.color};`">
                        PSS-SAMPLE-0000
                    </span>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-app-border bg-white p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">Field Positions</h2>
            <p class="mt-1 text-xs text-secondary">Top/Left are percentages from the image's top-left corner — each field is centered on that point. Sensible defaults are already filled in; change only what you need.</p>

            <div class="mt-4 space-y-4">
                @foreach ($fieldLabels as $key => $label)
                    <div class="rounded-lg border border-app-border p-4">
                        <p class="text-sm font-semibold text-secondary-dark">{{ $label }}</p>
                        <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div>
                                <x-input-label value="Top %" class="text-xs uppercase tracking-wide" />
                                <input type="number" min="0" max="100" step="0.5" name="fields[{{ $key }}][top]" x-model.number="fields.{{ $key }}.top"
                                    class="mt-1 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                            </div>
                            <div>
                                <x-input-label value="Left %" class="text-xs uppercase tracking-wide" />
                                <input type="number" min="0" max="100" step="0.5" name="fields[{{ $key }}][left]" x-model.number="fields.{{ $key }}.left"
                                    class="mt-1 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                            </div>
                            <div>
                                <x-input-label value="Font Size" class="text-xs uppercase tracking-wide" />
                                <input type="number" min="6" max="120" name="fields[{{ $key }}][font_size]" x-model.number="fields.{{ $key }}.font_size"
                                    class="mt-1 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                            </div>
                            <div>
                                <x-input-label value="Color" class="text-xs uppercase tracking-wide" />
                                <input type="color" name="fields[{{ $key }}][color]" x-model="fields.{{ $key }}.color"
                                    class="mt-1 h-9 w-full rounded-lg border-app-border shadow-sm">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <x-primary-button>Save Certificate Template</x-primary-button>
        </div>
    </form>
</div>
