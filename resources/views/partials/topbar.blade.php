<header class="sticky top-0 z-20 flex h-20 items-center gap-4 border-b border-slate-200 bg-white/80 px-4 backdrop-blur lg:px-8 print:hidden">
    <button type="button" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden" @click="sidebarOpen = !sidebarOpen">
        <x-icon name="menu" class="h-6 w-6" />
    </button>

    <div class="min-w-0 flex-1">
        <h1 class="flex items-center gap-1.5 truncate text-lg font-bold text-slate-800 sm:text-xl">
            {{ $title ?? 'Dashboard' }}
            @isset($titleIcon)
                <x-icon :name="$titleIcon" class="h-4 w-4 shrink-0 text-brand-500" />
            @endisset
        </h1>
        @isset($subtitle)
            <p class="hidden truncate text-sm text-slate-400 sm:block">{{ $subtitle }}</p>
        @endisset
    </div>

    @unless (auth()->user()->isAdmin())
        @php
            $socialLinks = [
                ['label' => 'Join Global Garner on WhatsApp', 'url' => 'https://chat.whatsapp.com/KvNzx3JmfkFF46QHEoFH7E', 'platform' => 'whatsapp', 'color' => 'bg-[#25D366]'],
                ['label' => 'Join Global Garner on Telegram', 'url' => 'https://t.me/globalgarnergroup', 'platform' => 'telegram', 'color' => 'bg-[#229ED9]'],
                ['label' => 'Join Global Garner on Instagram', 'url' => 'https://www.instagram.com/global_garner_official/', 'platform' => 'instagram', 'color' => 'bg-gradient-to-br from-[#f58529] via-[#dd2a7b] to-[#8134af]'],
                ['label' => 'Join Global Garner on LinkedIn', 'url' => 'https://www.linkedin.com/company/global-garner-sales-services-limited/', 'platform' => 'linkedin', 'color' => 'bg-[#0A66C2]'],
                ['label' => 'Join Our WhatsApp Group for New Prospect', 'url' => 'https://chat.whatsapp.com/B8acaayRBj3HXldl6mCvvZ?mode=ems_copy_c', 'platform' => 'whatsapp', 'color' => 'bg-[#25D366]'],
            ];

            // Toggle button shows one small circle per distinct platform below, in a
            // stacked cluster — automatically follows whatever platforms are added above.
            $distinctPlatforms = collect($socialLinks)->unique('platform')->values();
        @endphp
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <div class="relative hidden p-2 sm:block">
                <div class="absolute inset-0 rounded-full border border-dashed border-brand-300">
                    <span class="absolute -left-1 top-1/2 h-1.5 w-1.5 -translate-y-1/2 rounded-full bg-brand-400"></span>
                    <span class="absolute -right-1 top-1/2 h-1.5 w-1.5 -translate-y-1/2 rounded-full bg-brand-400"></span>
                </div>

                <button type="button" @click="open = !open" title="Join Us To Know More"
                    class="relative flex items-center rounded-full border border-slate-200 bg-white py-2 pl-2 pr-3 shadow-sm transition hover:shadow-md">
                    @foreach ($distinctPlatforms as $index => $link)
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white ring-2 ring-white {{ $link['color'] }} {{ $index > 0 ? '-ml-3' : '' }}">
                            <x-brand-icon name="{{ $link['platform'] }}" class="h-5 w-5" />
                        </span>
                    @endforeach
                </button>
            </div>

            <div x-show="open" x-transition x-cloak class="absolute right-0 top-12 z-30 w-80 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                <p class="flex items-center gap-1.5 border-b border-slate-100 px-4 py-3 text-sm font-bold text-slate-800">
                    <x-icon name="grid" class="h-4 w-4 text-brand-600" />
                    Join Us To Know More
                </p>
                <div class="py-1">
                    @foreach ($socialLinks as $link)
                        <a href="{{ $link['url'] }}" target="_blank" rel="noopener" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white {{ $link['color'] }}">
                                <x-brand-icon name="{{ $link['platform'] }}" class="h-4 w-4" />
                            </span>
                            <span class="flex-1 text-sm font-medium text-slate-700">{{ $link['label'] }}</span>
                            <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-slate-300" />
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        @php
            $points = auth()->user()->combinedPoints();

            // TEMP: static breakdown so the dropdown has something to show.
            // Replace with real per-category point data once that's tracked.
            $pointsBreakdown = [
                ['label' => 'Training Completion', 'sub' => 'Completed onboarding training', 'icon' => 'academic-cap', 'color' => 'bg-violet-100 text-violet-600', 'value' => 500],
                ['label' => 'Assessments', 'sub' => 'Completed 2 assessments', 'icon' => 'edit', 'color' => 'bg-pink-100 text-pink-500', 'value' => 300],
                ['label' => 'Certificates Earned', 'sub' => 'Earned 1 certificate', 'icon' => 'badge', 'color' => 'bg-blue-100 text-blue-500', 'value' => 250],
                ['label' => 'Daily Login', 'sub' => 'Logged in today', 'icon' => 'calendar', 'color' => 'bg-orange-100 text-orange-500', 'value' => 50],
                ['label' => 'Bonus Points', 'sub' => 'Achieved bonus milestone', 'icon' => 'star', 'color' => 'bg-amber-100 text-amber-500', 'value' => 150],
            ];
        @endphp

        <div class="relative hidden sm:block" x-data="{ pointsOpen: false }" @click.outside="pointsOpen = false">
            <button
                type="button"
                @click="pointsOpen = !pointsOpen"
                class="flex items-center gap-2.5 rounded-2xl border border-dashed border-brand-300 bg-brand-50 py-1.5 pl-2 pr-3 transition hover:bg-brand-100"
            >
                <span class="flex h-12 w-12 shrink-0 items-center justify-center">
                    <img src="{{ asset('images/coins.png') }}" alt="Coins" class="h-12 w-12 object-contain" />
                </span>

                <span class="text-left leading-tight">
                    <span class="block text-xs font-semibold text-brand-700">Earning Points</span>
                    <span class="flex items-baseline gap-1">
                        <span id="topbar-points-earned" class="text-base font-bold text-slate-800">{{ $points->earned }}</span>
                        <span class="text-xs font-semibold text-brand-600">pts</span>
                    </span>
                </span>

                <span class="block shrink-0 text-brand-400 transition-transform" :class="pointsOpen ? 'rotate-180' : ''">
                    <x-icon name="chevron-down" class="h-4 w-4" />
                </span>
            </button>

            <div
                x-show="pointsOpen"
                x-transition
                x-cloak
                class="absolute right-0 top-14 z-30 w-80 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg"
            >
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <p class="text-sm font-bold text-slate-800">Earning Points Overview</p>
                    <x-icon name="trending-up" class="h-4 w-4 text-brand-600" />
                </div>

                <div class="space-y-4 p-4">
                    <div class="flex items-center justify-between rounded-xl bg-amber-50 px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center">
                                <img src="{{ asset('images/coins.png') }}" alt="Coins" class="h-14 w-14 object-contain" />
                            </span>
                            <span>
                                <span class="block text-sm font-semibold text-slate-800">Total Points</span>
                                <span class="block text-xs text-slate-400">Keep learning and earning!</span>
                            </span>
                        </div>
                        <span class="whitespace-nowrap text-lg font-bold text-brand-700">
                            <span id="topbar-points-earned-total">{{ $points->earned }}</span>
                            <span class="text-xs font-semibold text-brand-600">pts</span>
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach ($pointsBreakdown as $row)
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $row['color'] }}">
                                        <x-icon name="{{ $row['icon'] }}" class="h-4 w-4" />
                                    </span>
                                    <span>
                                        <span class="block text-sm font-semibold text-slate-700">{{ $row['label'] }}</span>
                                        <span class="block text-xs text-slate-400">{{ $row['sub'] }}</span>
                                    </span>
                                </div>
                                <span class="whitespace-nowrap text-sm font-semibold text-emerald-600">+{{ $row['value'] }} pts</span>
                            </div>
                        @endforeach
                    </div>

                    <span
                        title="Coming soon"
                        class="flex cursor-not-allowed items-center justify-center gap-2 rounded-xl bg-brand-50 px-4 py-2.5 text-sm font-semibold text-brand-300"
                    >
                        <x-icon name="gift" class="h-4 w-4" />
                        View Points History
                    </span>
                </div>
            </div>
        </div>

        <script>
            // Called after any client-side action that can change the user's points
            // (e.g. answering a Resource video quiz) so the badge above updates
            // immediately, without needing a full page reload.
            window.refreshPointsBadge = function () {
                window.axios.get('{{ route('user.points.show') }}').then(({ data }) => {
                    document.querySelectorAll('#topbar-points-earned, #topbar-points-earned-total').forEach((el) => {
                        el.textContent = data.earned;
                    });
                });
            };
        </script>
    @endunless

    <button type="button" class="relative rounded-full p-2.5 text-slate-500 hover:bg-slate-100">
        <x-icon name="bell" class="h-5 w-5" />
        <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-brand-600"></span>
    </button>

    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
        <button
            type="button"
            @click="open = !open"
            class="hidden h-9 w-9 items-center justify-center rounded-full bg-brand-700 text-sm font-semibold text-white transition hover:opacity-90 sm:flex"
        >
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </button>

        <div
            x-show="open"
            x-transition
            x-cloak
            class="absolute right-0 top-12 z-30 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg"
        >
            <div class="border-b border-slate-100 px-4 py-3">
                <p class="truncate text-sm font-semibold text-slate-700">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
            </div>

            <div class="py-1">
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.settings') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
                        <x-icon name="cog" class="h-4 w-4" />
                        <span>Settings</span>
                    </a>
                @else
                    <a href="{{ route('user.profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
                        <x-icon name="user" class="h-4 w-4" />
                        <span>Profile</span>
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-slate-600 hover:bg-slate-50">
                        <x-icon name="logout" class="h-4 w-4" />
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
