<x-layout title="Certificates">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Certificates</h1>
        <p class="mt-1 text-sm text-secondary">Earned automatically when you complete a course that has a certificate.</p>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($certificates as $certificate)
            <a href="{{ route('user.certificates.show', $certificate) }}" class="overflow-hidden rounded-xl border border-app-border bg-white hover:border-primary/40 hover:shadow-sm">
                @if ($certificate->course->certificateBackgroundUrl())
                    <img src="{{ $certificate->course->certificateBackgroundUrl() }}" alt="" class="aspect-video w-full object-cover">
                @else
                    <div class="flex aspect-video w-full items-center justify-center bg-primary-light text-primary">
                        <x-icon name="badge" class="w-8 h-8" />
                    </div>
                @endif
                <div class="p-5">
                    <h2 class="truncate font-semibold text-secondary-dark">{{ $certificate->course->title }}</h2>
                    <p class="mt-1 text-xs text-secondary">Issued {{ $certificate->issued_at->format('d M Y') }}</p>
                    <p class="mt-1 text-xs text-secondary">{{ $certificate->certificate_number }}</p>
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                Complete a course with a certificate to see it here.
            </div>
        @endforelse
    </div>
</x-layout>
