<div class="relative hidden overflow-hidden bg-gradient-to-br from-brand-700 via-brand-800 to-brand-900 lg:flex lg:w-1/2 lg:flex-col xl:w-[58%]">
    <div class="relative z-10 flex h-full flex-col px-8 py-8 xl:px-12 2xl:px-16">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white p-1.5 shadow-lg">
                <img src="{{ asset('favicon.png') }}" alt="Global Garner Hub" class="h-full w-full object-contain" />
            </div>
            <div class="leading-tight">
                <p class="text-lg font-bold text-white tracking-tight">Global Garner Hub</p>
                <p class="text-xs font-medium uppercase tracking-wider text-brand-300">Learn · Assess · Grow</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="mt-10 max-w-md 2xl:max-w-lg">
            <h1 class="text-2xl font-extrabold leading-tight text-white xl:text-3xl 2xl:text-[2.25rem]">
                Know More. Do Better.
                <span class="relative inline-block whitespace-nowrap text-brand-300">
                    Grow Faster.
                    <span class="absolute -bottom-1.5 left-0 h-1 w-full rounded-full bg-brand-400/50"></span>
                </span>
            </h1>
            <p class="mt-5 max-w-[26rem] text-sm leading-relaxed text-white/80 xl:text-base">
                Learn the Global Garner ecosystem, assess your knowledge, and build the skills to grow.
            </p>

            <!-- Feature List -->
            <div class="mt-9 space-y-5">
                <div class="flex items-start gap-4 border-l-2 border-white/10 pl-4 transition-colors hover:border-brand-300">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/10 text-brand-200">
                        <x-icon name="book-open" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="font-semibold text-white">Learn</p>
                        <p class="text-sm text-white/70">Explore GG resources.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 border-l-2 border-white/10 pl-4 transition-colors hover:border-brand-300">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/10 text-brand-200">
                        <x-icon name="check-circle" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="font-semibold text-white">Assess</p>
                        <p class="text-sm text-white/70">Test your knowledge.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 border-l-2 border-white/10 pl-4 transition-colors hover:border-brand-300">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/10 text-brand-200">
                        <x-icon name="trending-up" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="font-semibold text-white">Grow</p>
                        <p class="text-sm text-white/70">Improve and qualify.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-auto flex items-center gap-2 pt-8 text-xs text-white/50 border-t border-white/10">
            <x-icon name="lock" class="h-3.5 w-3.5" />
            <span>&copy; {{ date('Y') }} Global Garner Hub. All rights reserved.</span>
        </div>
    </div>

    <!-- Illustration -->
    <div
        class="absolute bottom-0 right-0 z-0 h-auto w-[55%] opacity-20"
        style="-webkit-mask-image: linear-gradient(to left, black 70%, transparent 100%); mask-image: linear-gradient(to left, black 70%, transparent 100%);"
    >
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