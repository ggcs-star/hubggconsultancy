<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} · GG Hub</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- course-player.js must load (and register its window listeners) before Alpine
         starts — Alpine's init() dispatches the first course:load-item via $nextTick,
         which resolves before any script placed later in the page would get to run. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/course-player.js', 'resources/js/charts.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body
    class="font-sans print:bg-white"
    x-data="{ sidebarOpen: false, sidebarCollapsed: localStorage.getItem('sidebar-collapsed') === '1' }"
    x-init="$watch('sidebarCollapsed', value => localStorage.setItem('sidebar-collapsed', value ? '1' : '0'))"
>

    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden" @click="sidebarOpen = false"></div>

    @include('partials.sidebar')

    <div class="flex min-h-screen flex-col transition-[padding] duration-200 print:pl-0" :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-72'">
        @include('partials.topbar', ['title' => $title ?? null, 'subtitle' => $subtitle ?? null, 'titleIcon' => $titleIcon ?? null])

        <main class="flex-1 px-4 py-6 lg:px-8 lg:py-8 print:p-0">
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <x-confirm-modal />

    @stack('scripts')

</body>
</html>
