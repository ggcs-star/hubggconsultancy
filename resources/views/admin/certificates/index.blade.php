<x-layout title="Certificates">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <div class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-light">
                    <x-icon
                        name="badge"
                        class="h-5 w-5 text-primary"
                    />
                </div>

                <div>
                    <h1 class="text-2xl font-semibold text-secondary-dark">
                        Certificates
                    </h1>

                    <p class="mt-1 text-sm text-secondary">
                        Configure course certificates and manage certificates earned by trainees.
                    </p>
                </div>
            </div>
        </div>

    </div>


    {{-- Stats --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">

        {{-- Courses --}}
        <div class="rounded-xl border border-app-border bg-white p-5">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                        Total Courses
                    </p>

                    <p class="mt-2 text-2xl font-bold text-secondary-dark">
                        {{ $courses->count() }}
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50">
                    <x-icon
                        name="academic-cap"
                        class="h-5 w-5 text-blue-600"
                    />
                </div>

            </div>

        </div>


        {{-- Configured --}}
        <div class="rounded-xl border border-app-border bg-white p-5">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                        Configured
                    </p>

                    <p class="mt-2 text-2xl font-bold text-secondary-dark">
                        {{ $courses->filter(fn ($course) => $course->hasCertificateTemplate())->count() }}
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-50">
                    <x-icon
                        name="check-circle"
                        class="h-5 w-5 text-green-600"
                    />
                </div>

            </div>

        </div>


        {{-- Issued --}}
        <div class="rounded-xl border border-app-border bg-white p-5">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                        Issued Certificates
                    </p>

                    <p class="mt-2 text-2xl font-bold text-secondary-dark">
                        {{ $certificates->total() }}
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50">
                    <x-icon
                        name="badge"
                        class="h-5 w-5 text-amber-600"
                    />
                </div>

            </div>

        </div>

    </div>


    {{-- Course Certificate Templates --}}
    <div class="mt-6 overflow-hidden rounded-xl border border-app-border bg-white">

        <div class="flex flex-col gap-3 border-b border-app-border px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-base font-semibold text-secondary-dark">
                    Certificate Templates
                </h2>

                <p class="mt-1 text-xs text-secondary">
                    Configure certificate templates for your courses.
                </p>
            </div>

            <div class="inline-flex items-center gap-2 rounded-lg bg-primary-light px-3 py-2 text-xs font-medium text-primary">

                <x-icon
                    name="document"
                    class="h-4 w-4"
                />

                {{ $courses->count() }} Courses

            </div>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-app-border text-sm">

                <thead class="bg-surface-alt">

                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">

                        <th class="px-5 py-3">
                            Course
                        </th>

                        <th class="px-5 py-3">
                            Certificate Status
                        </th>

                        <th class="px-5 py-3">
                            Template
                        </th>

                        <th class="px-5 py-3 text-right">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-app-border">

                    @forelse ($courses as $course)

                        <tr class="transition hover:bg-surface-alt/50">

                            {{-- Course --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light">

                                        <x-icon
                                            name="academic-cap"
                                            class="h-5 w-5 text-primary"
                                        />

                                    </div>

                                    <div class="min-w-0">

                                        <p class="font-semibold text-secondary-dark">
                                            {{ $course->title }}
                                        </p>

                                        @if ($course->description)

                                            <p class="mt-1 max-w-md truncate text-xs text-secondary">
                                                {{ $course->description }}
                                            </p>

                                        @endif

                                    </div>

                                </div>

                            </td>


                            {{-- Status --}}
                            <td class="px-5 py-4">

                                @if ($course->hasCertificateTemplate())

                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">

                                        <x-icon
                                            name="check-circle"
                                            class="h-3.5 w-3.5"
                                        />

                                        Configured

                                    </span>

                                @else

                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">

                                        <x-icon
                                            name="document"
                                            class="h-3.5 w-3.5"
                                        />

                                        Not Configured

                                    </span>

                                @endif

                            </td>


                            {{-- Template --}}
                            <td class="px-5 py-4">

                                @if ($course->hasCertificateTemplate())

                                    <div class="flex items-center gap-2 text-sm text-secondary-dark">

                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-50">

                                            <x-icon
                                                name="badge"
                                                class="h-4 w-4 text-green-600"
                                            />

                                        </span>

                                        <div>
                                            <p class="font-medium">
                                                Certificate Ready
                                            </p>

                                            <p class="text-xs text-secondary">
                                                Available for completed learners
                                            </p>
                                        </div>

                                    </div>

                                @else

                                    <div class="flex items-center gap-2 text-sm text-secondary">

                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50">

                                            <x-icon
                                                name="document"
                                                class="h-4 w-4 text-secondary"
                                            />

                                        </span>

                                        <span>
                                            Setup required
                                        </span>

                                    </div>

                                @endif

                            </td>


                            {{-- Action --}}
                            <td class="px-5 py-4 text-right">

                                <a
                                    href="{{ route('admin.courses.show', [
                                        'course' => $course,
                                        'tab' => 'certificate'
                                    ]) }}"
                                    class="inline-flex items-center gap-2 rounded-lg border border-primary/20 bg-primary-light px-3 py-2 text-xs font-semibold text-primary transition hover:bg-primary hover:text-white"
                                >

                                    <x-icon
                                        name="edit"
                                        class="h-3.5 w-3.5"
                                    />

                                    {{ $course->hasCertificateTemplate() ? 'Edit Certificate' : 'Configure Certificate' }}

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="px-5 py-14 text-center"
                            >

                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-primary-light">

                                    <x-icon
                                        name="academic-cap"
                                        class="h-7 w-7 text-primary"
                                    />

                                </div>

                                <h3 class="mt-4 text-sm font-semibold text-secondary-dark">
                                    No courses available
                                </h3>

                                <p class="mx-auto mt-1 max-w-md text-xs text-secondary">
                                    Create a course under LMS / Courses first, then configure its certificate.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- Issued Certificates --}}
    <div class="mt-6 overflow-hidden rounded-xl border border-app-border bg-white">

        <div class="flex flex-col gap-3 border-b border-app-border px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2 class="text-base font-semibold text-secondary-dark">
                    Issued Certificates
                </h2>

                <p class="mt-1 text-xs text-secondary">
                    Certificates already earned by trainees.
                </p>

            </div>


            @if ($certificates->total() > 0)

                <span class="inline-flex items-center gap-2 rounded-lg bg-green-50 px-3 py-2 text-xs font-semibold text-green-700">

                    <x-icon
                        name="check-circle"
                        class="h-4 w-4"
                    />

                    {{ $certificates->total() }} Issued

                </span>

            @endif

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-app-border text-sm">

                <thead class="bg-surface-alt">

                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">

                        <th class="px-5 py-3">
                            Trainee
                        </th>

                        <th class="px-5 py-3">
                            Course
                        </th>

                        <th class="px-5 py-3">
                            Certificate ID
                        </th>

                        <th class="px-5 py-3">
                            Issued Date
                        </th>

                        <th class="px-5 py-3 text-right">
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-app-border">

                    @forelse ($certificates as $certificate)

                        <tr class="transition hover:bg-surface-alt/50">

                            {{-- Trainee --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-xs font-bold text-white">

                                        {{ strtoupper(substr($certificate->user->name ?? 'U', 0, 1)) }}

                                    </div>

                                    <div>

                                        <p class="font-semibold text-secondary-dark">
                                            {{ $certificate->user->name ?? 'Unknown User' }}
                                        </p>

                                        @if ($certificate->user?->email)

                                            <p class="text-xs text-secondary">
                                                {{ $certificate->user->email }}
                                            </p>

                                        @endif

                                    </div>

                                </div>

                            </td>


                            {{-- Course --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center gap-2">

                                    <x-icon
                                        name="academic-cap"
                                        class="h-4 w-4 text-primary"
                                    />

                                    <span class="font-medium text-secondary-dark">
                                        {{ $certificate->course->title ?? '—' }}
                                    </span>

                                </div>

                            </td>


                            {{-- Certificate ID --}}
                            <td class="px-5 py-4">

                                <code class="rounded-lg bg-slate-50 px-2.5 py-1.5 text-xs font-semibold text-secondary-dark">
                                    {{ $certificate->certificate_number }}
                                </code>

                            </td>


                            {{-- Date --}}
                            <td class="px-5 py-4 text-secondary">

                                <div class="flex items-center gap-2">

                                    <x-icon
                                        name="calendar"
                                        class="h-4 w-4 text-secondary"
                                    />

                                    {{ $certificate->issued_at?->format('d M Y') ?? '—' }}

                                </div>

                            </td>


                            {{-- Status --}}
                            <td class="px-5 py-4 text-right">

                                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">

                                    <x-icon
                                        name="check-circle"
                                        class="h-3.5 w-3.5"
                                    />

                                    Issued

                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-5 py-14 text-center"
                            >

                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-slate-50">

                                    <x-icon
                                        name="badge"
                                        class="h-7 w-7 text-secondary"
                                    />

                                </div>

                                <h3 class="mt-4 text-sm font-semibold text-secondary-dark">
                                    No certificates issued yet
                                </h3>

                                <p class="mx-auto mt-1 max-w-md text-xs text-secondary">
                                    Certificates will appear here when trainees complete a course and earn their certificate.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if ($certificates->hasPages())

            <div class="border-t border-app-border px-5 py-4">

                {{ $certificates->links() }}

            </div>

        @endif

    </div>

</x-layout>