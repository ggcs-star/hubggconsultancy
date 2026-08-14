<x-layout title="Onboarding Assessment" subtitle="Test your knowledge and see how you score">

    <div class="card p-10 text-center">
        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-50 text-amber-600">
            <x-icon name="user" class="h-6 w-6" />
        </span>
        <p class="mt-4 font-bold text-slate-800">Complete your profile first</p>
        <p class="mt-1 text-sm text-slate-400">You need to complete your profile before you can take the onboarding assessment.</p>
        <a href="{{ route('user.profile') }}" class="btn btn-primary mt-5 inline-flex">Complete your profile</a>
    </div>

</x-layout>
