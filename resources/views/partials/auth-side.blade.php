<div class="relative hidden overflow-hidden bg-gradient-to-br from-brand-100 via-brand-50 to-white lg:flex lg:w-1/2 lg:flex-col xl:w-[58%]">
    <div class="relative z-10 flex h-full flex-col px-10 py-8 xl:px-16">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-700 text-white">
                <x-icon name="academic-cap" class="h-6 w-6" />
            </div>
            <span class="text-lg font-bold text-slate-800">Pre Sales School</span>
        </div>

        <div class="mt-10 max-w-[260px]">
            <h1 class="text-4xl font-extrabold leading-tight text-slate-800 xl:text-[2.75rem]">
                Turn every rep<br>into a <span class="text-brand-700">closer.</span>
            </h1>
            <p class="mt-5 text-sm text-slate-500">
                Onboard, train and certify your sales team with courses, manuals and
                ready-to-share social content — all in one place.
            </p>

            <div class="mt-9 space-y-5">
                <div class="flex items-center gap-4">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white text-brand-700 shadow-sm ring-1 ring-slate-100">
                        <x-icon name="trending-up" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="font-semibold text-slate-800">Track Progress</p>
                        <p class="text-sm text-slate-500">Follow every learner from sign-up to certification.</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white text-brand-700 shadow-sm ring-1 ring-slate-100">
                        <x-icon name="book-open" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="font-semibold text-slate-800">One Learning Hub</p>
                        <p class="text-sm text-slate-500">Courses, manuals and guides kept in one place.</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white text-brand-700 shadow-sm ring-1 ring-slate-100">
                        <x-icon name="users" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="font-semibold text-slate-800">For Admins & Sales Team</p>
                        <p class="text-sm text-slate-500">Role-based access for every kind of user.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-auto flex items-center gap-2 pt-10 text-sm text-slate-400">
            <x-icon name="lock" class="h-4 w-4" />
            &copy; {{ date('Y') }} Pre Sales School. All rights reserved.
        </div>
    </div>

    <img
        id="auth-illustration"
        src="{{ asset('images/login.png') }}"
        alt="Sales rep reviewing training progress on a dashboard"
        class="pointer-events-none absolute bottom-0 right-0 z-0 w-[58%] h-auto max-w-none object-contain object-bottom"
    >
</div>
