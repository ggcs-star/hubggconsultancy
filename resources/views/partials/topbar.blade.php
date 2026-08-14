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
        @php $lmsPoints = auth()->user()->lmsPoints(); @endphp
        <div class="hidden items-center gap-1.5 rounded-full bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-700 sm:flex" title="Points earned across your LMS course quizzes">
            <x-icon name="trending-up" class="h-4 w-4" />
            <span>{{ $lmsPoints->earned }}/{{ $lmsPoints->total }}</span>
            <span class="text-xs font-medium text-amber-500">pts</span>
        </div>
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
