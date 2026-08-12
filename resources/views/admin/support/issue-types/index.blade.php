<x-layout title="Support Issue Types">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

        <div>

            <h1 class="text-2xl font-semibold text-secondary-dark">
                Support Issue Types
            </h1>

            <p class="mt-1 text-sm text-secondary">
                Manage product-specific issues that clients can raise for SaaS products.
            </p>

        </div>


        <div class="flex items-center gap-3">

            <a
                href="{{ route('admin.support.issue-types.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary/90"
            >

                <x-icon
                    name="plus"
                    class="h-4 w-4"
                />

                Add Issue Type

            </a>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SUCCESS MESSAGE --}}
    {{-- ========================================================= --}}

    @if (session('success'))

        <div class="mt-5 rounded-lg border border-success/20 bg-success-light px-4 py-3 text-sm text-success">

            {{ session('success') }}

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- ERROR MESSAGE --}}
    {{-- ========================================================= --}}

    @if (session('error'))

        <div class="mt-5 rounded-lg border border-danger/20 bg-danger-light px-4 py-3">

            <p class="text-sm font-semibold text-danger">
                {{ session('error') }}
            </p>

        </div>

    @endif


    @if ($errors->any())

        <div class="mt-5 rounded-lg border border-danger/20 bg-danger-light px-4 py-3">

            <p class="text-sm font-semibold text-danger">
                Please fix the following errors:
            </p>

            <ul class="mt-2 list-disc pl-5 text-sm text-danger">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- FILTERS --}}
    {{-- ========================================================= --}}

    <form
        method="GET"
        action="{{ route('admin.support.issue-types.index') }}"
        class="mt-6 rounded-xl border border-app-border bg-white p-4"
    >

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-12">


            {{-- Search --}}
            <div class="min-w-0 lg:col-span-4">

                <label
                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary"
                >
                    Search
                </label>


                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Issue, module or product..."
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                >

            </div>


            {{-- SaaS Product --}}
            <div class="min-w-0 lg:col-span-3">

                <label
                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary"
                >
                    SaaS Product
                </label>


                <select
                    name="saas_product_id"
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                >

                    <option value="">
                        All Products
                    </option>


                    @foreach ($products as $product)

                        <option
                            value="{{ $product->id }}"
                            @selected(
                                request('saas_product_id') == $product->id
                            )
                        >
                            {{ $product->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- Module --}}
            <div class="min-w-0 lg:col-span-2">

                <label
                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary"
                >
                    Module
                </label>


                <select
                    name="module"
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                >

                    <option value="">
                        All Modules
                    </option>

                    <option
                        value="dashboard"
                        @selected(request('module') === 'dashboard')
                    >
                        Dashboard
                    </option>

                    <option
                        value="payment"
                        @selected(request('module') === 'payment')
                    >
                        Payment
                    </option>

                    <option
                        value="products"
                        @selected(request('module') === 'products')
                    >
                        Products
                    </option>

                    <option
                        value="system"
                        @selected(request('module') === 'system')
                    >
                        System
                    </option>

                    <option
                        value="website"
                        @selected(request('module') === 'website')
                    >
                        Website
                    </option>

                    <option
                        value="api"
                        @selected(request('module') === 'api')
                    >
                        API
                    </option>

                </select>

            </div>


            {{-- Status --}}
            <div class="min-w-0 lg:col-span-1">

                <label
                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary"
                >
                    Status
                </label>


                <select
                    name="status"
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                >

                    <option value="">
                        All
                    </option>

                    <option
                        value="1"
                        @selected(request('status') === '1')
                    >
                        Active
                    </option>

                    <option
                        value="0"
                        @selected(request('status') === '0')
                    >
                        Inactive
                    </option>

                </select>

            </div>


            {{-- Search Button --}}
            <div class="flex items-end lg:col-span-2">

                <div class="flex w-full gap-2">

                    <a
                        href="{{ route('admin.support.issue-types.index') }}"
                        class="inline-flex flex-1 items-center justify-center rounded-lg border border-app-border bg-white px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt"
                    >
                        Reset
                    </a>


                    <x-primary-button
                        type="submit"
                        class="flex-1"
                    >

                        <x-icon
                            name="search"
                            class="h-4 w-4"
                        />

                        Search

                    </x-primary-button>

                </div>

            </div>

        </div>

    </form>


    {{-- ========================================================= --}}
    {{-- ISSUE TYPES TABLE --}}
    {{-- ========================================================= --}}

    <div class="mt-6 overflow-x-auto rounded-xl border border-app-border bg-white">

        <table class="min-w-full divide-y divide-app-border text-sm">

            <thead>

                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">

                    <th class="px-4 py-3">
                        Issue Type
                    </th>

                    <th class="px-4 py-3">
                        SaaS Product
                    </th>

                    <th class="px-4 py-3">
                        Module
                    </th>

                    <th class="px-4 py-3">
                        Priority
                    </th>

                    <th class="px-4 py-3">
                        Status
                    </th>

                    <th class="px-4 py-3 text-right">
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-app-border">

                @forelse ($issueTypes as $issueType)

                    <tr class="hover:bg-surface-alt">


                        {{-- ================================================= --}}
                        {{-- ISSUE TYPE --}}
                        {{-- ================================================= --}}

                        <td class="px-4 py-3">

                            <div class="flex items-center gap-3">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">

                                    <x-icon
                                        name="{{ $issueType->icon ?: 'help-circle' }}"
                                        class="h-4 w-4"
                                    />

                                </div>


                                <div class="min-w-0">

                                    <div class="font-medium text-secondary-dark">

                                        {{ $issueType->name }}

                                    </div>


                                    @if ($issueType->description)

                                        <div class="max-w-xs truncate text-xs text-secondary">

                                            {{ $issueType->description }}

                                        </div>

                                    @endif

                                </div>

                            </div>

                        </td>


                        {{-- ================================================= --}}
                        {{-- SAAS PRODUCT --}}
                        {{-- ================================================= --}}

                        <td class="px-4 py-3">

                            @if ($issueType->product)

                                <div class="flex items-center gap-2.5">

                                    @if (!empty($issueType->product->logo))

                                        <img
                                            src="{{ asset('storage/' . ltrim($issueType->product->logo, '/')) }}"
                                            alt="{{ $issueType->product->name }}"
                                            class="h-8 w-8 rounded-lg border border-app-border bg-white object-contain p-1"
                                        >

                                    @else

                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">

                                            <x-icon
                                                name="cube"
                                                class="h-4 w-4"
                                            />

                                        </div>

                                    @endif


                                    <div class="min-w-0">

                                        <p
                                            class="max-w-[180px] truncate font-medium text-secondary-dark"
                                            title="{{ $issueType->product->name }}"
                                        >
                                            {{ $issueType->product->name }}
                                        </p>

                                    </div>

                                </div>

                            @else

                                <span class="text-xs text-danger">
                                    Product not assigned
                                </span>

                            @endif

                        </td>


                        {{-- ================================================= --}}
                        {{-- MODULE --}}
                        {{-- ================================================= --}}

                        <td class="px-4 py-3 text-secondary-dark">

                            @if ($issueType->module)

                                {{ ucfirst($issueType->module) }}

                            @else

                                <span class="text-secondary">
                                    —
                                </span>

                            @endif

                        </td>


                        {{-- ================================================= --}}
                        {{-- PRIORITY --}}
                        {{-- ================================================= --}}

                        <td class="px-4 py-3">

                            @php

                                $priorityClasses = match (
                                    $issueType->default_priority
                                ) {

                                    'urgent' =>
                                        'bg-danger-light text-danger',

                                    'high' =>
                                        'bg-danger-light text-danger',

                                    'medium' =>
                                        'bg-warning-light text-warning',

                                    default =>
                                        'bg-primary-light text-primary',

                                };

                            @endphp


                            <span
                                class="rounded-full px-2.5 py-1 text-xs font-medium {{ $priorityClasses }}"
                            >

                                {{ ucfirst($issueType->default_priority) }}

                            </span>

                        </td>


                        {{-- ================================================= --}}
                        {{-- STATUS --}}
                        {{-- ================================================= --}}

                        <td class="px-4 py-3">

                            @if ($issueType->status)

                                <span class="rounded-full bg-success-light px-2.5 py-1 text-xs font-medium text-success">

                                    Active

                                </span>

                            @else

                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">

                                    Inactive

                                </span>

                            @endif

                        </td>


                        {{-- ================================================= --}}
                        {{-- ACTIONS --}}
                        {{-- ================================================= --}}

                        <td class="px-4 py-3">

                            <div class="flex items-center justify-end gap-1.5">


                                {{-- Edit --}}
                                <a
                                    href="{{ route('admin.support.issue-types.edit', $issueType) }}"
                                    title="Edit"
                                    class="inline-flex items-center justify-center rounded-md border border-warning/30 bg-warning-light p-1.5 text-warning hover:border-warning/60 hover:bg-warning/20"
                                >

                                    <x-icon
                                        name="edit"
                                        class="h-3.5 w-3.5"
                                    />

                                </a>


                                {{-- Toggle Status --}}
                                <form
                                    method="POST"
                                    action="{{ route('admin.support.issue-types.toggle-status', $issueType) }}"
                                >

                                    @csrf

                                    @method('PATCH')


                                    <button
                                        type="submit"
                                        title="{{ $issueType->status ? 'Deactivate' : 'Activate' }}"
                                        class="inline-flex items-center justify-center rounded-md border border-primary/30 bg-primary-light p-1.5 text-primary hover:border-primary/60"
                                    >

                                        <x-icon
                                            name="{{ $issueType->status ? 'eye-off' : 'eye' }}"
                                            class="h-3.5 w-3.5"
                                        />

                                    </button>

                                </form>


                                {{-- Delete --}}
                                <form
                                    method="POST"
                                    action="{{ route('admin.support.issue-types.destroy', $issueType) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this issue type?');"
                                >

                                    @csrf

                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        title="Delete"
                                        class="inline-flex items-center justify-center rounded-md border border-danger/30 bg-danger-light p-1.5 text-danger hover:border-danger/60 hover:bg-danger/20"
                                    >

                                        <x-icon
                                            name="trash"
                                            class="h-3.5 w-3.5"
                                        />

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="px-4 py-12 text-center"
                        >

                            <div class="flex flex-col items-center">

                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-secondary">

                                    <x-icon
                                        name="help-circle"
                                        class="h-6 w-6"
                                    />

                                </div>


                                <p class="mt-3 text-sm font-semibold text-secondary-dark">

                                    No issue types found

                                </p>


                                <p class="mt-1 text-xs text-secondary">

                                    Create a product-specific issue type that clients can select when raising a ticket.

                                </p>


                                <a
                                    href="{{ route('admin.support.issue-types.create') }}"
                                    class="mt-4 text-sm font-medium text-primary hover:underline"
                                >

                                    Add Issue Type

                                </a>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- ========================================================= --}}
    {{-- PAGINATION --}}
    {{-- ========================================================= --}}

    @if ($issueTypes->hasPages())

        <div class="mt-5">

            {{ $issueTypes->links() }}

        </div>

    @endif

</x-layout>