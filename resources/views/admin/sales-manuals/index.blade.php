<x-layout title="Sales Manuals">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">
                Sales Manuals
            </h1>

            <p class="mt-1 text-sm text-secondary">
                Manage sales guides, cheat sheets, scripts, FAQs and other sales resources.
            </p>
        </div>

        <a
            href="{{ route('admin.manuals.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90"
        >
            <x-icon name="plus" class="h-4 w-4" />
            Create Manual
        </a>

    </div>


    {{-- Success Message --}}
    @if (session('success'))
        <div class="mt-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif


    {{-- Error Message --}}
    @if (session('error'))
        <div class="mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif


    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3">

            <ul class="list-disc space-y-1 pl-5 text-sm text-red-700">

                @foreach ($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach

            </ul>

        </div>
    @endif


    {{-- Filters --}}
    <div class="mt-6 rounded-xl border border-app-border bg-white p-4">

        <form
            method="GET"
            action="{{ route('admin.manuals.index') }}"
            class="grid grid-cols-1 gap-3 md:grid-cols-4"
        >

            {{-- Search --}}
            <div class="md:col-span-2">

                <label
                    for="search"
                    class="sr-only"
                >
                    Search
                </label>

                <div class="relative">

                    <x-icon
                        name="search"
                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary"
                    />

                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search manuals..."
                        class="w-full rounded-lg border-app-border py-2.5 pl-9 pr-3 text-sm shadow-sm focus:border-primary focus:ring-primary"
                    >

                </div>

            </div>


            {{-- Type --}}
            <div>

                <select
                    name="type"
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                >

                    <option value="">
                        All Types
                    </option>

                    <option
                        value="manual"
                        @selected(request('type') === 'manual')
                    >
                        Manual
                    </option>

                    <option
                        value="guide"
                        @selected(request('type') === 'guide')
                    >
                        Guide
                    </option>

                    <option
                        value="cheat_sheet"
                        @selected(request('type') === 'cheat_sheet')
                    >
                        Cheat Sheet
                    </option>

                    <option
                        value="faq"
                        @selected(request('type') === 'faq')
                    >
                        FAQ
                    </option>

                    <option
                        value="sop"
                        @selected(request('type') === 'sop')
                    >
                        SOP
                    </option>

                    <option
                        value="script"
                        @selected(request('type') === 'script')
                    >
                        Script
                    </option>

                </select>

            </div>


            {{-- Status --}}
            <div>

                <select
                    name="status"
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                >

                    <option value="">
                        All Status
                    </option>

                    <option
                        value="published"
                        @selected(request('status') === 'published')
                    >
                        Published
                    </option>

                    <option
                        value="draft"
                        @selected(request('status') === 'draft')
                    >
                        Draft
                    </option>

                </select>

            </div>


            {{-- Filter Buttons --}}
            <div class="flex gap-2 md:col-span-4">

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90"
                >

                    <x-icon
                        name="filter"
                        class="h-4 w-4"
                    />

                    Filter

                </button>


                <a
                    href="{{ route('admin.manuals.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-app-border bg-white px-4 py-2.5 text-sm font-medium text-secondary-dark transition hover:bg-surface-alt"
                >

                    <x-icon
                        name="refresh"
                        class="h-4 w-4"
                    />

                    Reset

                </a>

            </div>

        </form>

    </div>


    {{-- Manuals Table --}}
    <div class="mt-6 rounded-xl border border-app-border bg-white">

        {{-- Table Header --}}
        <div class="border-b border-app-border px-5 py-4">

            <h2 class="text-base font-semibold text-secondary-dark">
                All Sales Manuals
            </h2>

            <p class="mt-1 text-xs text-secondary">
                {{ $manuals->total() }}
                total manual{{ $manuals->total() == 1 ? '' : 's' }}
            </p>

        </div>


        @if ($manuals->count())

            <div class="overflow-x-auto">

                <table class="w-full min-w-[1150px]">

                    <thead>

                        <tr class="border-b border-app-border bg-surface-alt">

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                                Manual
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                                Type
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                                Category
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                                Status
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                                Visibility
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                                Files
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-secondary">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-app-border">

                        @foreach ($manuals as $manual)

                            <tr class="transition hover:bg-surface-alt/50">

                                {{-- Manual --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-primary-light">

                                            @if ($manual->cover_image)

                                                <img
                                                    src="{{ asset('storage/' . $manual->cover_image) }}"
                                                    alt="{{ $manual->title }}"
                                                    class="h-full w-full object-cover"
                                                >

                                            @else

                                                <x-icon
                                                    name="document"
                                                    class="h-5 w-5 text-primary"
                                                />

                                            @endif

                                        </div>


                                        <div class="min-w-0">

                                            <div class="flex flex-wrap items-center gap-2">

                                                <p class="truncate text-sm font-semibold text-secondary-dark">
                                                    {{ $manual->title }}
                                                </p>


                                                {{-- Pinned --}}
                                                @if ($manual->is_pinned)

                                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-semibold text-amber-700">

                                                        <x-icon
                                                            name="pin"
                                                            class="h-3 w-3"
                                                        />

                                                        Pinned

                                                    </span>

                                                @endif


                                                {{-- Featured --}}
                                                @if ($manual->is_featured)

                                                    <span class="inline-flex items-center gap-1 rounded-full bg-primary-light px-2 py-0.5 text-[10px] font-semibold text-primary">

                                                        <x-icon
                                                            name="star"
                                                            class="h-3 w-3"
                                                        />

                                                        Featured

                                                    </span>

                                                @endif

                                            </div>


                                            @if ($manual->description)

                                                <p class="mt-1 max-w-md truncate text-xs text-secondary">
                                                    {{ $manual->description }}
                                                </p>

                                            @endif

                                        </div>

                                    </div>

                                </td>


                                {{-- Type --}}
                                <td class="px-5 py-4">

                                    @php

                                        $typeLabels = [
                                            'manual' => 'Manual',
                                            'guide' => 'Guide',
                                            'cheat_sheet' => 'Cheat Sheet',
                                            'faq' => 'FAQ',
                                            'sop' => 'SOP',
                                            'script' => 'Script',
                                        ];

                                        $typeIcons = [
                                            'manual' => 'document',
                                            'guide' => 'academic-cap',
                                            'cheat_sheet' => 'list',
                                            'faq' => 'help-circle',
                                            'sop' => 'check-circle',
                                            'script' => 'pencil',
                                        ];

                                    @endphp

                                    <div class="flex items-center gap-2">

                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-50 text-secondary">

                                            <x-icon
                                                name="{{ $typeIcons[$manual->type] ?? 'document' }}"
                                                class="h-4 w-4"
                                            />

                                        </span>

                                        <span class="text-sm text-secondary-dark">
                                            {{ $typeLabels[$manual->type] ?? ucfirst($manual->type) }}
                                        </span>

                                    </div>

                                </td>


                                {{-- Category --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-2">

                                        <x-icon
                                            name="folder"
                                            class="h-4 w-4 text-secondary"
                                        />

                                        <span class="text-sm text-secondary">
                                            {{ $manual->category ?: '—' }}
                                        </span>

                                    </div>

                                </td>


                                {{-- Status --}}
                                <td class="px-5 py-4">

                                    @if ($manual->status === 'published')

                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">

                                            <x-icon
                                                name="check-circle"
                                                class="h-3.5 w-3.5"
                                            />

                                            Published

                                        </span>

                                    @else

                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">

                                            <x-icon
                                                name="document"
                                                class="h-3.5 w-3.5"
                                            />

                                            Draft

                                        </span>

                                    @endif

                                </td>


                                {{-- Visibility --}}
                                <td class="px-5 py-4">

                                    @if ($manual->is_active)

                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">

                                            <x-icon
                                                name="eye"
                                                class="h-3.5 w-3.5"
                                            />

                                            Active

                                        </span>

                                    @else

                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-600">

                                            <x-icon
                                                name="eye-off"
                                                class="h-3.5 w-3.5"
                                            />

                                            Inactive

                                        </span>

                                    @endif

                                </td>


                                {{-- Files --}}
                                <td class="px-5 py-4">

                                    <span class="inline-flex items-center gap-1.5 text-sm text-secondary-dark">

                                        <x-icon
                                            name="paper-clip"
                                            class="h-4 w-4 text-secondary"
                                        />

                                        {{ $manual->attachments_count }}

                                    </span>

                                </td>


                                {{-- Actions --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-end gap-1">

                                        {{-- View --}}
                                        <a
                                            href="{{ route('admin.manuals.show', $manual) }}"
                                            title="Preview"
                                            class="rounded-lg p-2 text-secondary transition hover:bg-surface-alt hover:text-primary"
                                        >

                                            <x-icon
                                                name="eye"
                                                class="h-4 w-4"
                                            />

                                        </a>


                                        {{-- Edit --}}
                                        <a
                                            href="{{ route('admin.manuals.edit', $manual) }}"
                                            title="Edit"
                                            class="rounded-lg p-2 text-secondary transition hover:bg-surface-alt hover:text-primary"
                                        >

                                            <x-icon
                                                name="pencil"
                                                class="h-4 w-4"
                                            />

                                        </a>


                                        {{-- Publish / Draft --}}
                                        <form
                                            method="POST"
                                            action="{{ route('admin.manuals.publish', $manual) }}"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                title="{{ $manual->status === 'published' ? 'Move to Draft' : 'Publish' }}"
                                                class="rounded-lg p-2 text-secondary transition hover:bg-surface-alt hover:text-primary"
                                            >

                                                @if ($manual->status === 'published')

                                                    <x-icon
                                                        name="eye-off"
                                                        class="h-4 w-4"
                                                    />

                                                @else

                                                    <x-icon
                                                        name="check-circle"
                                                        class="h-4 w-4"
                                                    />

                                                @endif

                                            </button>

                                        </form>


                                        {{-- Active / Inactive --}}
                                        <form
                                            method="POST"
                                            action="{{ route('admin.manuals.active', $manual) }}"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                title="{{ $manual->is_active ? 'Deactivate' : 'Activate' }}"
                                                class="rounded-lg p-2 text-secondary transition hover:bg-surface-alt hover:text-primary"
                                            >

                                                @if ($manual->is_active)

                                                    <x-icon
                                                        name="toggle-right"
                                                        class="h-4 w-4"
                                                    />

                                                @else

                                                    <x-icon
                                                        name="toggle-left"
                                                        class="h-4 w-4"
                                                    />

                                                @endif

                                            </button>

                                        </form>


                                        {{-- Featured --}}
                                        <form
                                            method="POST"
                                            action="{{ route('admin.manuals.featured', $manual) }}"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                title="{{ $manual->is_featured ? 'Remove Featured' : 'Make Featured' }}"
                                                class="rounded-lg p-2 text-secondary transition hover:bg-surface-alt hover:text-amber-600"
                                            >

                                                <x-icon
                                                    name="star"
                                                    class="h-4 w-4"
                                                />

                                            </button>

                                        </form>


                                        {{-- Pinned --}}
                                        <form
                                            method="POST"
                                            action="{{ route('admin.manuals.pinned', $manual) }}"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                title="{{ $manual->is_pinned ? 'Unpin' : 'Pin' }}"
                                                class="rounded-lg p-2 text-secondary transition hover:bg-surface-alt hover:text-amber-600"
                                            >

                                                <x-icon
                                                    name="pin"
                                                    class="h-4 w-4"
                                                />

                                            </button>

                                        </form>


                                        {{-- Delete --}}
                                        <form
                                            method="POST"
                                            action="{{ route('admin.manuals.destroy', $manual) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this sales manual?');"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                title="Delete"
                                                class="rounded-lg p-2 text-secondary transition hover:bg-red-50 hover:text-red-600"
                                            >

                                                <x-icon
                                                    name="trash"
                                                    class="h-4 w-4"
                                                />

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if ($manuals->hasPages())

                <div class="border-t border-app-border px-5 py-4">

                    {{ $manuals->links() }}

                </div>

            @endif

        @else

            {{-- Empty State --}}
            <div class="px-6 py-16 text-center">

                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-primary-light">

                    <x-icon
                        name="document"
                        class="h-7 w-7 text-primary"
                    />

                </div>


                <h3 class="mt-4 text-base font-semibold text-secondary-dark">
                    No sales manuals found
                </h3>


                <p class="mx-auto mt-1 max-w-md text-sm text-secondary">

                    @if (
                        request()->filled('search') ||
                        request()->filled('type') ||
                        request()->filled('status')
                    )

                        Try changing your search or filters.

                    @else

                        Create your first sales manual, guide or cheat sheet.

                    @endif

                </p>


                @if (
                    !request()->filled('search') &&
                    !request()->filled('type') &&
                    !request()->filled('status')
                )

                    <a
                        href="{{ route('admin.manuals.create') }}"
                        class="mt-5 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90"
                    >

                        <x-icon
                            name="plus"
                            class="h-4 w-4"
                        />

                        Create Manual

                    </a>

                @endif

            </div>

        @endif

    </div>

</x-layout>