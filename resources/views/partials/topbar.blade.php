<header class="sticky top-0 z-20 flex h-20 items-center gap-4 border-b border-slate-200 bg-white/80 px-4 backdrop-blur lg:px-8 print:hidden">
    <button type="button" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden" @click="sidebarOpen = !sidebarOpen">
        <x-icon name="menu" class="h-6 w-6" />
    </button>

    <div class="min-w-0 flex-1">
        <h1 class="truncate text-lg font-bold text-slate-800 sm:text-xl">{{ $title ?? 'Dashboard' }}</h1>
        @isset($subtitle)
            <p class="hidden truncate text-sm text-slate-400 sm:block">{{ $subtitle }}</p>
        @endisset
    </div>

    <button type="button" class="relative rounded-full p-2.5 text-slate-500 hover:bg-slate-100">
        <x-icon name="bell" class="h-5 w-5" />
        <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-brand-600"></span>
    </button>

    <div class="hidden h-9 w-9 items-center justify-center rounded-full bg-brand-700 text-sm font-semibold text-white sm:flex">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
    </div>
</header>
