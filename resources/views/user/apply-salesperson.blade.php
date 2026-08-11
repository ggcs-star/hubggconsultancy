<x-layout title="Apply to be Salesperson" subtitle="Join the Pre Sales School sales team">

    <div class="card mx-auto max-w-2xl p-8 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50 text-brand-700">
            <x-icon name="briefcase" class="h-8 w-8" />
        </div>

        @if ($user->salesperson_status === 'none')
            <h2 class="mt-5 text-xl font-bold text-slate-800">Ready to become a salesperson?</h2>
            <p class="mt-2 text-sm text-slate-500">
                Submit your application and our team will review it. Once approved, you'll get access to
                training courses, certificates, sales manuals and the social media guide.
            </p>

            @unless ($user->profile_completed)
                <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    Please <a href="{{ route('user.profile') }}" class="font-semibold underline">complete your profile</a> before applying.
                </div>
            @else
                <form method="POST" action="{{ route('user.apply-salesperson.apply') }}" class="mt-6">
                    @csrf
                    <button type="submit" class="btn-primary">Submit Application</button>
                </form>
            @endunless
        @elseif ($user->salesperson_status === 'pending')
            <span class="badge badge-amber mx-auto mt-5"><x-icon name="clock" class="h-3.5 w-3.5" /> Pending Review</span>
            <h2 class="mt-3 text-xl font-bold text-slate-800">Your application is under review</h2>
            <p class="mt-2 text-sm text-slate-500">Our team is reviewing your application. We'll notify you once a decision is made.</p>
        @elseif ($user->salesperson_status === 'approved')
            <span class="badge badge-green mx-auto mt-5"><x-icon name="check-circle" class="h-3.5 w-3.5" /> Approved</span>
            <h2 class="mt-3 text-xl font-bold text-slate-800">You're a Pre Sales School salesperson!</h2>
            <p class="mt-2 text-sm text-slate-500">Head over to Training / LMS to start your certification courses.</p>
            <a href="{{ route('user.training') }}" class="btn-primary mt-6 inline-flex">Start Training</a>
        @else
            <span class="badge badge-slate mx-auto mt-5">Rejected</span>
            <h2 class="mt-3 text-xl font-bold text-slate-800">Your application wasn't approved this time</h2>
            <p class="mt-2 text-sm text-slate-500">Make sure your profile is complete and up to date, then try applying again.</p>
            <form method="POST" action="{{ route('user.apply-salesperson.apply') }}" class="mt-6">
                @csrf
                <button type="submit" class="btn-primary">Re-apply</button>
            </form>
        @endif
    </div>

</x-layout>
