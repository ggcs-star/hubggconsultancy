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
                ['label' => 'Join Our WhatsApp Group for New Prospect', 'url' => 'https://chat.whatsapp.com/B8acaayRBj3HXldl6mCvvZ?mode=ems_copy_c', 'platform' => 'whatsapp', 'color' => 'bg-[#25D366]'],
            ];

            // Toggle button shows one small circle per distinct platform below, in a
            // stacked cluster — automatically follows whatever platforms are added above.
            $distinctPlatforms = collect($socialLinks)->unique('platform')->values();
        @endphp
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <button type="button" @click="open = !open" title="Join Us To Know More"
                class="hidden items-center rounded-full border border-slate-200 bg-white py-1.5 pl-1.5 pr-2.5 shadow-sm transition hover:shadow-md sm:flex">
                @foreach ($distinctPlatforms as $index => $link)
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-white ring-2 ring-white {{ $link['color'] }} {{ $index > 0 ? '-ml-2' : '' }}">
                        <x-brand-icon name="{{ $link['platform'] }}" class="h-3.5 w-3.5" />
                    </span>
                @endforeach
            </button>

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

        @php $points = auth()->user()->combinedPoints(); @endphp
        <div class="hidden items-center gap-1.5 rounded-full bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-700 sm:flex" title="Points earned across your LMS course and Resource video quizzes">
            <x-icon name="trending-up" class="h-4 w-4" />
            <span><span id="topbar-points-earned">{{ $points->earned }}</span>/<span id="topbar-points-total">{{ $points->total }}</span></span>
            <span class="text-xs font-medium text-amber-500">pts</span>
        </div>

        <script>
            // Called after any client-side action that can change the user's points
            // (e.g. answering a Resource video quiz) so the badge above updates
            // immediately, without needing a full page reload.
            window.refreshPointsBadge = function () {
                window.axios.get('{{ route('user.points.show') }}').then(({ data }) => {
                    const earnedEl = document.getElementById('topbar-points-earned');
                    const totalEl = document.getElementById('topbar-points-total');
                    if (earnedEl) earnedEl.textContent = data.earned;
                    if (totalEl) totalEl.textContent = data.total;
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
