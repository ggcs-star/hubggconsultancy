<x-layout title="Certificates">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Certificates</h1>
        <p class="mt-1 text-sm text-secondary">Set up a certificate per course, then track who's earned one.</p>
    </div>

    <div class="mt-6 rounded-xl border border-app-border bg-white">
        <div class="border-b border-app-border px-4 py-3">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">Course Templates</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-app-border text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                        <th class="px-4 py-3">Course</th>
                        <th class="px-4 py-3">Certificate</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border">
                    @forelse ($courses as $course)
                        <tr>
                            <td class="px-4 py-3 font-medium text-secondary-dark">{{ $course->title }}</td>
                            <td class="px-4 py-3">
                                <x-badge :classes="$course->hasCertificateTemplate() ? 'bg-success-light text-success' : 'bg-secondary-light text-secondary-dark'" dot>
                                    {{ $course->hasCertificateTemplate() ? 'Configured' : 'Not set up' }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.courses.show', ['course' => $course, 'tab' => 'certificate']) }}"
                                    class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:underline">
                                    <x-icon name="edit" class="w-3.5 h-3.5" />
                                    {{ $course->hasCertificateTemplate() ? 'Edit' : 'Configure' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-sm text-secondary">
                                No courses yet — create one under LMS / Courses first.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-app-border bg-white">
        <div class="border-b border-app-border px-4 py-3">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">Issued Certificates</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-app-border text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                        <th class="px-4 py-3">Trainee</th>
                        <th class="px-4 py-3">Course</th>
                        <th class="px-4 py-3">Certificate ID</th>
                        <th class="px-4 py-3">Issued</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border">
                    @forelse ($certificates as $certificate)
                        <tr>
                            <td class="px-4 py-3 font-medium text-secondary-dark">{{ $certificate->user->name }}</td>
                            <td class="px-4 py-3 text-secondary-dark">{{ $certificate->course->title }}</td>
                            <td class="px-4 py-3 text-secondary">{{ $certificate->certificate_number }}</td>
                            <td class="px-4 py-3 text-secondary">{{ $certificate->issued_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-sm text-secondary">
                                No certificates have been earned yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($certificates->hasPages())
            <div class="border-t border-app-border px-4 py-3">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
</x-layout>
