<div class="relative hidden overflow-hidden bg-gradient-to-br from-brand-700 via-brand-800 to-brand-900 lg:flex lg:w-1/2 lg:flex-col xl:w-[58%]">
    <div class="relative z-10 flex h-full flex-col px-8 py-8 xl:px-12 2xl:px-16">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-white shadow-md backdrop-blur-sm">
                <x-icon name="academic-cap" class="h-5 w-5" />
            </div>
            <span class="text-lg font-bold text-white tracking-tight">Pre Sales School</span>
        </div>

        <!-- Main Content -->
        <div class="mt-8 max-w-sm 2xl:max-w-md">
            <h1 class="text-3xl font-extrabold leading-tight text-white xl:text-4xl 2xl:text-[2.75rem]">
                Turn every rep<br>into a <span class="text-brand-300 relative">
                    closer.
                    <span class="absolute -bottom-1 left-0 w-full h-1 bg-brand-400/50 rounded-full"></span>
                </span>
            </h1>
            <p class="mt-4 text-sm text-white/80 leading-relaxed xl:text-base">
                Onboard, train and certify your sales team with courses, manuals and
                ready-to-share social content — all in one place.
            </p>

            <!-- Feature List -->
            <div class="mt-8 space-y-4">
                <div class="flex items-start gap-4 group">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white transition-all group-hover:bg-white/20 group-hover:scale-105">
                        <x-icon name="trending-up" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="font-semibold text-white">Track Progress</p>
                        <p class="text-sm text-white/70">Follow every learner from sign-up to certification.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 group">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white transition-all group-hover:bg-white/20 group-hover:scale-105">
                        <x-icon name="book-open" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="font-semibold text-white">One Learning Hub</p>
                        <p class="text-sm text-white/70">Courses, manuals and guides kept in one place.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 group">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white transition-all group-hover:bg-white/20 group-hover:scale-105">
                        <x-icon name="users" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="font-semibold text-white">For Admins & Sales Team</p>
                        <p class="text-sm text-white/70">Role-based access for every kind of user.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-auto flex items-center gap-2 pt-8 text-xs text-white/50 border-t border-white/10">
            <x-icon name="lock" class="h-3.5 w-3.5" />
            <span>&copy; {{ date('Y') }} Pre Sales School. All rights reserved.</span>
        </div>
    </div>

    <!-- Illustration -->
    <div class="absolute bottom-0 right-0 z-0 w-[55%] h-auto opacity-20">
        <img
            id="auth-illustration"
            src="{{ asset('images/login.png') }}"
            alt="Sales rep reviewing training progress on a dashboard"
            class="pointer-events-none w-full h-auto max-w-none object-contain object-bottom"
            loading="lazy"
            decoding="async"
        >
    </div>
</div>