@php
$isAdmin = auth()->user()->isAdmin();

$mainNav = $isAdmin
    ? [
        ['label' => 'Dashboard', 'icon' => 'grid', 'route' => 'admin.dashboard'],
        ['label' => 'Clients', 'icon' => 'users', 'route' => 'admin.clients'],
        ['label' => 'Salesperson Applications', 'icon' => 'briefcase', 'route' => 'admin.salesperson-applications'],
    ]
    : [
        ['label' => 'Dashboard', 'icon' => 'grid', 'route' => 'user.dashboard'],
        ['label' => 'My Profile', 'icon' => 'user', 'route' => 'user.profile'],
        ['label' => 'Apply to be Salesperson', 'icon' => 'briefcase', 'route' => 'user.apply-salesperson'],
    ];

$contentNav = $isAdmin
    ? [
        ['label' => 'LMS / Courses', 'icon' => 'academic-cap', 'route' => 'admin.courses.index'],
        ['label' => 'Certificates', 'icon' => 'badge', 'route' => 'admin.certificates'],
        ['label' => 'Sales Manuals', 'icon' => 'document', 'route' => 'admin.manuals'],
        ['label' => 'Social Media Guide', 'icon' => 'share', 'route' => 'admin.social-guide'],
    ]
    : [
        ['label' => 'Training / LMS', 'icon' => 'academic-cap', 'route' => 'user.training'],
        ['label' => 'Certificates', 'icon' => 'badge', 'route' => 'user.certificates.index'],
        ['label' => 'Sales Manuals', 'icon' => 'document', 'route' => 'user.manuals'],
        ['label' => 'Social Media Guide', 'icon' => 'share', 'route' => 'user.social-guide'],
    ];
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col border-r border-brand-100 bg-brand-50 transition-all duration-200 lg:translate-x-0 print:hidden"
    :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'lg:w-20' : 'lg:w-72']"
>
    <div class="flex h-20 shrink-0 items-center gap-3 px-6 transition-all duration-200" :class="sidebarCollapsed ? 'lg:justify-center lg:px-3' : ''">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-700 text-white">
            <x-icon name="academic-cap" class="h-6 w-6" />
        </div>
        <div class="min-w-0 flex-1 leading-tight" x-show="!sidebarCollapsed" x-transition.opacity>
            <p class="truncate text-base font-bold text-slate-800">Pre Sales School</p>
            <p class="truncate text-xs text-slate-400">{{ $isAdmin ? 'Admin Panel' : 'Sales Team' }}</p>
        </div>
        <button
            type="button"
            class="hidden shrink-0 rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-brand-700 lg:flex"
            @click="sidebarCollapsed = !sidebarCollapsed"
            :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
        >
            <span class="block transition-transform duration-200" :class="sidebarCollapsed ? '' : 'rotate-180'">
                <x-icon name="chevron-right" class="h-4 w-4" />
            </span>
        </button>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto overflow-x-hidden px-4 pb-6">
        <div class="space-y-1">
            @foreach ($mainNav as $item)
                <a
                    href="{{ route($item['route']) }}"
                    title="{{ $item['label'] }}"
                    class="sidebar-link {{ request()->routeIs($item['route']) ? 'active' : '' }}"
                    :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''"
                >
                    <x-icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                    <span x-show="!sidebarCollapsed" x-transition.opacity>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>

        <div>
            <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400" x-show="!sidebarCollapsed" x-transition.opacity>{{ $isAdmin ? 'Content' : 'Learning' }}</p>
            <div class="space-y-1">
                @foreach ($contentNav as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        title="{{ $item['label'] }}"
                        class="sidebar-link {{ request()->routeIs($item['route']) ? 'active' : '' }}"
                        :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''"
                    >
                        <x-icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                        <span x-show="!sidebarCollapsed" x-transition.opacity>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        @if ($isAdmin)
            <div>
                <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400" x-show="!sidebarCollapsed" x-transition.opacity>Operations</p>
                <div class="space-y-1">
                    <a
                        href="{{ route('admin.settings') }}"
                        title="Settings"
                        class="sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}"
                        :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''"
                    >
                        <x-icon name="cog" class="h-5 w-5 shrink-0" />
                        <span x-show="!sidebarCollapsed" x-transition.opacity>Settings</span>
                    </a>
                </div>
            </div>
        @endif
    </nav>

    <div class="border-t border-brand-100 p-4">
        <div class="flex items-center gap-3" :class="sidebarCollapsed ? 'lg:justify-center' : ''">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-700 text-sm font-semibold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1 leading-tight" x-show="!sidebarCollapsed" x-transition.opacity>
                <p class="truncate text-sm font-semibold text-slate-700">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" x-show="!sidebarCollapsed" x-transition.opacity>
                @csrf
                <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700" title="Logout">
                    <x-icon name="logout" class="h-5 w-5" />
                </button>
            </form>
        </div>
    </div>
</aside>
