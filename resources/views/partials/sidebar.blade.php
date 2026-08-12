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
            ['label' => 'Sales Manuals', 'icon' => 'document', 'route' => 'admin.manuals.index'],
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
    class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col border-r border-slate-200 bg-white transition-transform duration-200 lg:translate-x-0 print:hidden"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>

    {{-- Logo --}}
    <div class="flex h-20 items-center gap-3 px-6">

        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-700 text-white">
            <x-icon name="academic-cap" class="h-6 w-6" />
        </div>

        <div class="leading-tight">
            <p class="text-base font-bold text-slate-800">
                Pre Sales School
            </p>

            <p class="text-xs text-slate-400">
                {{ $isAdmin ? 'Admin Panel' : 'Sales Team' }}
            </p>
        </div>

    </div>


    <nav class="flex-1 space-y-6 overflow-y-auto px-4 pb-6">


        {{-- Main Navigation --}}
        <div class="space-y-1">

            @foreach ($mainNav as $item)

                <a
                    href="{{ route($item['route']) }}"
                    class="sidebar-link {{ request()->routeIs($item['route']) ? 'active' : '' }}"
                >

                    <x-icon
                        :name="$item['icon']"
                        class="h-5 w-5 shrink-0"
                    />

                    <span>
                        {{ $item['label'] }}
                    </span>

                </a>

            @endforeach

        </div>


        {{-- Content / Learning --}}
        <div>

            <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                {{ $isAdmin ? 'Content' : 'Learning' }}
            </p>

            <div class="space-y-1">

                @foreach ($contentNav as $item)

                    @php
                        $isActive = request()->routeIs($item['route']);

                        if (
                            $isAdmin &&
                            $item['route'] === 'admin.manuals.index' &&
                            request()->routeIs('admin.manuals.*')
                        ) {
                            $isActive = true;
                        }
                    @endphp

                    <a
                        href="{{ route($item['route']) }}"
                        class="sidebar-link {{ $isActive ? 'active' : '' }}"
                    >

                        <x-icon
                            :name="$item['icon']"
                            class="h-5 w-5 shrink-0"
                        />

                        <span>
                            {{ $item['label'] }}
                        </span>

                    </a>

                @endforeach

            </div>

        </div>


        {{-- ADMIN SUPPORT --}}
        @if ($isAdmin)

            <div>

                <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Support
                </p>

                <div class="space-y-1">

                    {{-- Tickets --}}
                    <a
                        href="{{ route('admin.support.tickets.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.support.tickets.*') ? 'active' : '' }}"
                    >

                        <x-icon
                            name="help-circle"
                            class="h-5 w-5 shrink-0"
                        />

                        <span>
                            Tickets
                        </span>

                    </a>


                    {{-- Issue Types --}}
                    <a
                        href="{{ route('admin.support.issue-types.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.support.issue-types.*') ? 'active' : '' }}"
                    >

                        <x-icon
                            name="list"
                            class="h-5 w-5 shrink-0"
                        />

                        <span>
                            Issue Types
                        </span>

                    </a>

                </div>

            </div>

        @endif


        {{-- USER SUPPORT --}}
        @if (!$isAdmin)

            <div>

                <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Support
                </p>

                <div class="space-y-1">

                    <a
                        href="{{ route('user.support.tickets.index') }}"
                        class="sidebar-link {{ request()->routeIs('user.support.tickets.*') ? 'active' : '' }}"
                    >

                        <x-icon
                            name="help-circle"
                            class="h-5 w-5 shrink-0"
                        />

                        <span>
                            Support Tickets
                        </span>

                    </a>

                </div>

            </div>

        @endif

    </nav>


    {{-- User Profile --}}
    <div class="border-t border-slate-100 p-4">

        <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3">

            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-700 text-sm font-semibold text-white">

                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

            </div>


            <div class="min-w-0 flex-1 leading-tight">

                <p class="truncate text-sm font-semibold text-slate-700">
                    {{ auth()->user()->name }}
                </p>

                <p class="truncate text-xs text-slate-400">
                    {{ auth()->user()->email }}
                </p>

            </div>


            <form
                method="POST"
                action="{{ route('logout') }}"
            >

                @csrf

                <button
                    type="submit"
                    class="rounded-lg p-2 text-slate-400 transition hover:bg-white hover:text-brand-700"
                    title="Logout"
                >

                    <x-icon
                        name="logout"
                        class="h-5 w-5"
                    />

                </button>

            </form>

        </div>

    </div>

</aside>