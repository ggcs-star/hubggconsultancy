@php
    $isAdmin = auth()->user()->isAdmin();

    $navGroups = [];

    // Dashboard
    $navGroups[] = [
        'heading' => null,
        'items' => [
            ['label' => 'Dashboard', 'icon' => 'grid', 'route' => $isAdmin ? 'admin.dashboard' : 'user.dashboard'],
        ],
    ];

    // Existing admin-only items with no equivalent in the new menu structure stay where they were.
    if ($isAdmin) {
        $navGroups[] = [
            'heading' => 'Content',
            'items' => [
                ['label' => 'Users', 'icon' => 'users', 'route' => 'admin.clients', 'activePattern' => 'admin.clients*'],
            ],
        ];
    }

    $navGroups[] = [
        'heading' => 'Learn',
        'items' => [
            [
                'label' => 'Training / LMS',
                'icon' => 'academic-cap',
                'route' => $isAdmin ? 'admin.courses.index' : 'user.training',
                'activePattern' => $isAdmin ? 'admin.courses.*' : null,
            ],
            [
                'label' => 'Live & Recorded Training',
                'icon' => 'video',
                'route' => $isAdmin ? 'admin.resources.index' : 'user.resources.index',
                'activePattern' => $isAdmin ? 'admin.resources.*' : null,
            ],
            [
                'label' => 'Documents',
                'icon' => 'document',
                'route' => $isAdmin ? 'admin.documents.index' : 'user.documents.index',
                'activePattern' => $isAdmin ? 'admin.documents.*' : null,
            ],
            [
                'label' => 'Sales Manuals',
                'icon' => 'folder',
                'route' => $isAdmin ? 'admin.manuals.index' : 'user.manuals',
                'activePattern' => $isAdmin ? 'admin.manuals.*' : 'user.manuals*',
            ],
            [
                'label' => 'Sales Toolkit',
                'icon' => 'briefcase',
                'route' => $isAdmin ? 'admin.sales-toolkit.index' : 'user.sales-toolkit.index',
                'activePattern' => $isAdmin ? 'admin.sales-toolkit.*' : null,
            ],
            [
                'label' => 'Scripts & Objection Handling',
                'icon' => 'book-open',
                'route' => $isAdmin ? 'admin.scripts.index' : 'user.scripts.index',
                'activePattern' => $isAdmin ? 'admin.scripts.*' : null,
            ],
        ],
    ];

    $navGroups[] = [
        'heading' => 'Assess & Certify',
        'items' => [
            [
                'label' => 'Assessments',
                'icon' => 'check-circle',
                'route' => $isAdmin ? 'admin.onboarding-assessment.index' : 'user.onboarding-assessment.index',
                'activeCheck' => $isAdmin
                    ? fn () => request()->routeIs('admin.onboarding-assessment.*') && request()->query('tab', 'quizzes') !== 'results'
                    : null,
            ],
            [
                'label' => $isAdmin ? 'Results' : 'My Results',
                'icon' => 'grid',
                'route' => $isAdmin ? 'admin.onboarding-assessment.index' : 'user.onboarding-assessment.results',
                'params' => $isAdmin ? ['tab' => 'results'] : [],
                'activeCheck' => $isAdmin
                    ? fn () => request()->routeIs('admin.onboarding-assessment.*') && request()->query('tab') === 'results'
                    : null,
            ],
            $isAdmin ? [
                'label' => 'SalesPerson',
                'icon' => 'users',
                'route' => 'admin.salesperson-applications',
                'activePattern' => 'admin.salesperson-applications*',
            ] : null,
            [
                'label' => 'Learning Progress',
                'icon' => 'academic-cap',
                'route' => $isAdmin ? 'admin.learning-progress.index' : 'user.learning-progress.index',
            ],
            [
                'label' => 'Certificates',
                'icon' => 'badge',
                'route' => $isAdmin ? 'admin.certificates' : 'user.certificates.index',
                'activePattern' => $isAdmin ? null : 'user.certificates.*',
            ],
        ],
    ];

    $navGroups[count($navGroups) - 1]['items'] = array_values(array_filter($navGroups[count($navGroups) - 1]['items']));

    $navGroups[] = [
        'heading' => 'Get Started',
        'items' => [
            [
                'label' => 'Onboarding',
                'icon' => 'check-circle',
                'route' => $isAdmin ? 'admin.onboarding-checklist.index' : 'user.onboarding-checklist.index',
                'activePattern' => $isAdmin ? 'admin.onboarding-checklist.*' : null,
            ],
            ['label' => 'Partner Journey', 'icon' => 'sparkles', 'route' => null],
        ],
    ];

    $navGroups[] = [
        'heading' => 'Sell',
        'items' => [
            [
                'label' => 'Products & Opportunities',
                'icon' => 'sparkles',
                'route' => $isAdmin ? 'admin.saas-products.index' : 'user.saas-products.index',
                'activePattern' => $isAdmin ? 'admin.saas-products.*' : 'user.saas-products.*',
            ],
            [
                'label' => 'Leads / CRM',
                'icon' => 'users',
                'route' => $isAdmin ? 'admin.leads.index' : 'user.leads.index',
                'activePattern' => $isAdmin ? 'admin.leads.*' : 'user.leads.*',
            ],
            ['label' => 'Sales Calculators', 'icon' => 'grid', 'route' => null],
            [
                'label' => 'My Team',
                'icon' => 'users',
                'route' => $isAdmin ? 'admin.teams.index' : 'user.team.index',
                'activePattern' => $isAdmin ? 'admin.teams.*' : null,
            ],
        ],
    ];

    $performanceItems = [];
    if (! $isAdmin) {
        $performanceItems[] = [
            'label' => 'My Performance',
            'icon' => 'trending-up',
            'route' => 'user.performance.index',
        ];
    }

    $navGroups[] = [
        'heading' => 'Performance',
        'items' => [
            ...$performanceItems,
            [
                'label' => 'Ranking / Leaderboard',
                'icon' => 'star',
                'route' => $isAdmin ? 'admin.leaderboard.index' : 'user.leaderboard.index',
            ],
            [
                'label' => 'Contests',
                'icon' => 'gift',
                'route' => $isAdmin ? 'admin.contests.index' : 'user.contests.index',
                'activePattern' => $isAdmin ? 'admin.contests.*' : null,
            ],
            [
                'label' => 'Contest Tracker',
                'icon' => 'trending-up',
                'route' => $isAdmin ? 'admin.contest-tracker.index' : 'user.contest-tracker.index',
            ],
            [
                'label' => 'Incentives & Earnings',
                'icon' => 'coin',
                'route' => $isAdmin ? 'admin.incentives.index' : 'user.incentives.index',
            ],
        ],
    ];

    $navGroups[] = [
        'heading' => 'Recognition',
        'items' => [
            [
                'label' => 'Achievers',
                'icon' => 'star',
                'route' => $isAdmin ? 'admin.achievers.index' : 'user.achievers.index',
                'activePattern' => $isAdmin ? 'admin.achievers.*' : null,
            ],
            [
                'label' => 'Hall of Fame',
                'icon' => 'sparkles',
                'route' => $isAdmin ? 'admin.hall-of-fame.index' : 'user.hall-of-fame.index',
                'activePattern' => $isAdmin ? 'admin.hall-of-fame.*' : null,
            ],
            [
                'label' => 'Success Stories',
                'icon' => 'lightbulb',
                'route' => $isAdmin ? 'admin.success-stories.index' : 'user.success-stories.index',
                'activePattern' => $isAdmin ? 'admin.success-stories.*' : null,
            ],
        ],
    ];

    $navGroups[] = [
        'heading' => 'Events & Community',
        'items' => [
            [
                'label' => 'Announcements',
                'icon' => 'bell',
                'route' => $isAdmin ? 'admin.announcements.index' : 'user.announcements.index',
                'activePattern' => $isAdmin ? 'admin.announcements.*' : null,
            ],
            [
                'label' => 'Events & Webinars',
                'icon' => 'calendar',
                'route' => $isAdmin ? 'admin.events.index' : 'user.events.index',
                'activePattern' => $isAdmin ? 'admin.events.*' : null,
            ],
            [
                'label' => 'GG Community',
                'icon' => 'users',
                'route' => null,
                'url' => 'https://globalgarner.community/',
            ],
        ],
    ];

    $supportItems = [
        [
            'label' => 'Knowledge Base',
            'icon' => 'external-link',
            'route' => null,
            'url' => 'https://allinone.ggconsultancy.services/',
        ],
        [
            'label' => 'FAQ',
            'icon' => 'help-circle',
            'route' => $isAdmin ? 'admin.faqs.index' : 'user.faqs.index',
            'activePattern' => $isAdmin ? 'admin.faqs.*' : null,
        ],
        [
            'label' => 'Support Tickets',
            'icon' => 'help-circle',
            'route' => $isAdmin ? 'admin.support.tickets.index' : 'user.support.tickets.index',
            'activePattern' => $isAdmin ? 'admin.support.tickets.*' : 'user.support.tickets.*',
        ],
    ];

    // "Issue Types" has no equivalent in the new menu structure, so it stays in its existing spot.
    if ($isAdmin) {
        $supportItems[] = [
            'label' => 'Issue Types',
            'icon' => 'list',
            'route' => 'admin.support.issue-types.index',
            'activePattern' => 'admin.support.issue-types.*',
        ];
    }

    $navGroups[] = [
        'heading' => 'Support',
        'items' => $supportItems,
    ];
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col border-r border-brand-100 bg-brand-50 transition-all duration-200 lg:translate-x-0 print:hidden"
    :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'lg:w-20' : 'lg:w-72']"
>

    {{-- Logo --}}
    <div
        class="flex h-20 shrink-0 items-center gap-3 px-6 transition-all duration-200"
        :class="sidebarCollapsed ? 'lg:justify-center lg:px-3' : ''"
    >
        <img src="{{ asset('favicon.png') }}" alt="GG Hub" class="h-11 w-11 shrink-0 object-contain" />

        <div
            class="min-w-0 flex-1 leading-tight"
            x-show="!sidebarCollapsed"
            x-transition.opacity
        >
            <p class="truncate text-base font-bold text-slate-800">
                GG Hub
            </p>

            <p class="truncate text-xs text-slate-400">
                {{ $isAdmin ? 'Admin Panel' : 'Learn · Sell · Grow' }}
            </p>
        </div>

        <button
            type="button"
            class="hidden shrink-0 rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-brand-700 lg:flex"
            @click="sidebarCollapsed = !sidebarCollapsed"
            :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
        >
            <span
                class="block transition-transform duration-200"
                :class="sidebarCollapsed ? '' : 'rotate-180'"
            >
                <x-icon name="chevron-right" class="h-4 w-4" />
            </span>
        </button>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto overflow-x-hidden px-4 pb-6">

        @foreach ($navGroups as $group)
            <div>
                @if ($group['heading'])
                    <p
                        class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400"
                        x-show="!sidebarCollapsed"
                        x-transition.opacity
                    >
                        {{ $group['heading'] }}
                    </p>
                @endif

                <div class="space-y-1">
                    @foreach ($group['items'] as $item)
                        @if (!empty($item['url']))
                            <a
                                href="{{ $item['url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                title="{{ $item['label'] }}"
                                class="sidebar-link"
                                :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''"
                            >
                                <x-icon
                                    :name="$item['icon']"
                                    class="h-5 w-5 shrink-0"
                                />

                                <span
                                    x-show="!sidebarCollapsed"
                                    x-transition.opacity
                                >
                                    {{ $item['label'] }}
                                </span>
                            </a>
                        @elseif ($item['route'])
                            @php
                                $isActive = isset($item['activeCheck'])
                                    ? ($item['activeCheck'])()
                                    : (!empty($item['activePattern'])
                                        ? request()->routeIs($item['activePattern'])
                                        : request()->routeIs($item['route']));
                            @endphp

                            <a
                                href="{{ route($item['route'], $item['params'] ?? []) }}"
                                title="{{ $item['label'] }}"
                                class="sidebar-link {{ $isActive ? 'active' : '' }}"
                                :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''"
                            >
                                <x-icon
                                    :name="$item['icon']"
                                    class="h-5 w-5 shrink-0"
                                />

                                <span
                                    x-show="!sidebarCollapsed"
                                    x-transition.opacity
                                >
                                    {{ $item['label'] }}
                                </span>
                            </a>
                        @else
                            <span
                                title="{{ $item['label'] }} (coming soon)"
                                class="sidebar-link pointer-events-none cursor-not-allowed opacity-40"
                                :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''"
                            >
                                <x-icon
                                    :name="$item['icon']"
                                    class="h-5 w-5 shrink-0"
                                />

                                <span
                                    x-show="!sidebarCollapsed"
                                    x-transition.opacity
                                >
                                    {{ $item['label'] }}
                                </span>
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach

    </nav>

    {{-- User Profile --}}
    <div class="border-t border-brand-100 p-4">
        <div
            class="flex items-center gap-3"
            :class="sidebarCollapsed ? 'lg:justify-center' : ''"
        >
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-700 text-sm font-semibold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

            <div
                class="min-w-0 flex-1 leading-tight"
                x-show="!sidebarCollapsed"
                x-transition.opacity
            >
                <p class="truncate text-sm font-semibold text-slate-700">
                    {{ auth()->user()->name }}
                </p>

                <p class="truncate text-xs text-slate-400">
                    {{ auth()->user()->email ?? auth()->user()->phone ?? '—' }}
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('logout') }}"
                x-show="!sidebarCollapsed"
                x-transition.opacity
            >
                @csrf

                <button
                    type="submit"
                    class="rounded-lg p-2 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700"
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
