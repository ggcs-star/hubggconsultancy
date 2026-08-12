<x-layout title="Support Issue Types">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

        <div>

            <h1 class="text-2xl font-semibold text-secondary-dark">
                Support Issue Types
            </h1>

            <p class="mt-1 text-sm text-secondary">
                Manage predefined issues that clients can raise for SaaS product problems.
            </p>

        </div>


        <div class="flex items-center gap-3">

            <x-primary-button
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'add-issue-type')">

                <x-icon
                    name="plus"
                    class="h-4 w-4" />

                Add Issue Type

            </x-primary-button>

        </div>

    </div>


    {{-- Success Message --}}
    @if (session('success'))

        <div class="mt-5 rounded-lg border border-success/20 bg-success-light px-4 py-3 text-sm text-success">

            {{ session('success') }}

        </div>

    @endif


    {{-- Error Message --}}
    @if ($errors->any())

        <div class="mt-5 rounded-lg border border-danger/20 bg-danger-light px-4 py-3">

            <p class="text-sm font-semibold text-danger">
                Please fix the following errors:
            </p>

            <ul class="mt-2 list-disc pl-5 text-sm text-danger">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Filters --}}
    <form
        method="GET"
        action="{{ route('admin.support.issue-types.index') }}"
        class="mt-6 rounded-xl border border-app-border bg-white p-4">

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-12">


            {{-- Search --}}
            <div class="min-w-0 lg:col-span-5">

                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">
                    Search Issue
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search issue type..."
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">

            </div>


            {{-- Module --}}
            <div class="min-w-0 lg:col-span-4">

                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">
                    Module
                </label>

                <select
                    name="module"
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">

                    <option value="">
                        All Modules
                    </option>

                    <option
                        value="dashboard"
                        @selected(request('module') === 'dashboard')>
                        Dashboard
                    </option>

                    <option
                        value="payment"
                        @selected(request('module') === 'payment')>
                        Payment
                    </option>

                    <option
                        value="products"
                        @selected(request('module') === 'products')>
                        Products
                    </option>

                    <option
                        value="system"
                        @selected(request('module') === 'system')>
                        System
                    </option>

                    <option
                        value="website"
                        @selected(request('module') === 'website')>
                        Website
                    </option>

                    <option
                        value="api"
                        @selected(request('module') === 'api')>
                        API
                    </option>

                </select>

            </div>


            {{-- Status --}}
            <div class="min-w-0 lg:col-span-3">

                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">
                    Status
                </label>

                <select
                    name="status"
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">

                    <option value="">
                        All Statuses
                    </option>

                    <option
                        value="1"
                        @selected(request('status') === '1')>
                        Active
                    </option>

                    <option
                        value="0"
                        @selected(request('status') === '0')>
                        Inactive
                    </option>

                </select>

            </div>

        </div>


        {{-- Filter Buttons --}}
        <div class="mt-4 flex justify-end gap-2">

            <a
                href="{{ route('admin.support.issue-types.index') }}"
                class="inline-flex items-center justify-center rounded-lg border border-app-border bg-white px-4 py-2 text-sm font-medium text-secondary-dark hover:bg-surface-alt">

                Reset

            </a>


            <x-primary-button type="submit">

                <x-icon
                    name="search"
                    class="h-4 w-4" />

                Search

            </x-primary-button>

        </div>

    </form>


    {{-- Issue Types Table --}}
    <div class="mt-6 overflow-x-auto rounded-xl border border-app-border bg-white">

        <table class="min-w-full divide-y divide-app-border text-sm">

            <thead>

                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">

                    <th class="px-4 py-3">
                        Issue Type
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


                        {{-- Issue Type --}}
                        <td class="px-4 py-3">

                            <div class="flex items-center gap-3">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">

                                    <x-icon
                                        name="{{ $issueType->icon ?: 'help-circle' }}"
                                        class="h-4 w-4" />

                                </div>


                                <div class="min-w-0">

                                    <div class="font-medium text-secondary-dark">

                                        {{ $issueType->name }}

                                    </div>


                                    @if ($issueType->description)

                                        <div class="max-w-md truncate text-xs text-secondary">

                                            {{ $issueType->description }}

                                        </div>

                                    @endif

                                </div>

                            </div>

                        </td>


                        {{-- Module --}}
                        <td class="px-4 py-3 text-secondary-dark">

                            @if ($issueType->module)

                                {{ ucfirst($issueType->module) }}

                            @else

                                <span class="text-secondary">
                                    —
                                </span>

                            @endif

                        </td>


                        {{-- Priority --}}
                        <td class="px-4 py-3">

                            @php

                                $priorityClasses = match ($issueType->default_priority) {

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
                                class="rounded-full px-2.5 py-1 text-xs font-medium {{ $priorityClasses }}">

                                {{ ucfirst($issueType->default_priority) }}

                            </span>

                        </td>


                        {{-- Status --}}
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


                        {{-- Actions --}}
                        <td class="px-4 py-3">

                            <div class="flex items-center justify-end gap-1.5">


                                {{-- Edit --}}
                                <a
                                    href="{{ route('admin.support.issue-types.edit', $issueType) }}"
                                    title="Edit"
                                    class="inline-flex items-center justify-center rounded-md border border-warning/30 bg-warning-light p-1.5 text-warning hover:border-warning/60 hover:bg-warning/20">

                                    <x-icon
                                        name="edit"
                                        class="h-3.5 w-3.5" />

                                </a>


                                {{-- Toggle Status --}}
                                <form
                                    method="POST"
                                    action="{{ route('admin.support.issue-types.toggle-status', $issueType) }}">

                                    @csrf

                                    @method('PATCH')


                                    <button
                                        type="submit"
                                        title="{{ $issueType->status ? 'Deactivate' : 'Activate' }}"
                                        class="inline-flex items-center justify-center rounded-md border border-primary/30 bg-primary-light p-1.5 text-primary hover:border-primary/60">

                                        <x-icon
                                            name="{{ $issueType->status ? 'eye-off' : 'eye' }}"
                                            class="h-3.5 w-3.5" />

                                    </button>

                                </form>


                                {{-- Delete --}}
                                <form
                                    method="POST"
                                    action="{{ route('admin.support.issue-types.destroy', $issueType) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this issue type?');">

                                    @csrf

                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        title="Delete"
                                        class="inline-flex items-center justify-center rounded-md border border-danger/30 bg-danger-light p-1.5 text-danger hover:border-danger/60 hover:bg-danger/20">

                                        <x-icon
                                            name="trash"
                                            class="h-3.5 w-3.5" />

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>


                @empty

                    {{-- No Data --}}
                    <tr>

                        <td
                            colspan="5"
                            class="px-4 py-12 text-center">

                            <div class="flex flex-col items-center">


                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-secondary">

                                    <x-icon
                                        name="help-circle"
                                        class="h-6 w-6" />

                                </div>


                                <p class="mt-3 text-sm font-semibold text-secondary-dark">

                                    No issue types found

                                </p>


                                <p class="mt-1 text-xs text-secondary">

                                    Create an issue type that clients can select when raising a ticket.

                                </p>


                                <button
                                    type="button"
                                    x-data=""
                                    x-on:click="$dispatch('open-modal', 'add-issue-type')"
                                    class="mt-4 text-sm font-medium text-primary hover:underline">

                                    Add Issue Type

                                </button>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- Pagination --}}
    @if ($issueTypes->hasPages())

        <div class="mt-5">

            {{ $issueTypes->links() }}

        </div>

    @endif


    {{-- Add Issue Modal --}}
    <x-modal
        name="add-issue-type"
        :show="$errors->isNotEmpty()"
        focusable>

        <form
            method="POST"
            action="{{ route('admin.support.issue-types.store') }}"
            class="p-6">

            @csrf


            {{-- Modal Header --}}
            <div class="flex items-center justify-between">

                <h2 class="text-lg font-semibold text-secondary-dark">

                    Add Support Issue Type

                </h2>


                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="text-secondary hover:text-secondary-dark">

                    <x-icon
                        name="x"
                        class="h-5 w-5" />

                </button>

            </div>


            {{-- Form Fields --}}
            <div class="mt-6 space-y-5">


                {{-- Name --}}
                <div>

                    <x-input-label
                        for="name"
                        value="Issue Name *"
                        class="uppercase text-xs tracking-wide" />

                    <x-text-input
                        id="name"
                        name="name"
                        class="mt-1.5"
                        value="{{ old('name') }}"
                        placeholder="e.g. Payment Error"
                        required />

                </div>


                {{-- Module --}}
                <div>

                    <x-input-label
                        for="module"
                        value="Module"
                        class="uppercase text-xs tracking-wide" />


                    <select
                        id="module"
                        name="module"
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">

                        <option value="">
                            Select Module
                        </option>

                        <option
                            value="dashboard"
                            @selected(old('module') === 'dashboard')>
                            Dashboard
                        </option>

                        <option
                            value="payment"
                            @selected(old('module') === 'payment')>
                            Payment
                        </option>

                        <option
                            value="products"
                            @selected(old('module') === 'products')>
                            Products
                        </option>

                        <option
                            value="system"
                            @selected(old('module') === 'system')>
                            System
                        </option>

                        <option
                            value="website"
                            @selected(old('module') === 'website')>
                            Website
                        </option>

                        <option
                            value="api"
                            @selected(old('module') === 'api')>
                            API
                        </option>

                    </select>

                </div>


                {{-- Priority --}}
                <div>

                    <x-input-label
                        for="priority"
                        value="Default Priority"
                        class="uppercase text-xs tracking-wide" />


                    <select
                        id="priority"
                        name="default_priority"
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">

                        <option
                            value="low"
                            @selected(old('default_priority', 'medium') === 'low')>
                            Low
                        </option>

                        <option
                            value="medium"
                            @selected(old('default_priority', 'medium') === 'medium')>
                            Medium
                        </option>

                        <option
                            value="high"
                            @selected(old('default_priority', 'medium') === 'high')>
                            High
                        </option>

                        <option
                            value="urgent"
                            @selected(old('default_priority', 'medium') === 'urgent')>
                            Urgent
                        </option>

                    </select>

                </div>


                {{-- Description --}}
                <div>

                    <x-input-label
                        for="description"
                        value="Description"
                        class="uppercase text-xs tracking-wide" />


                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="Describe this issue type..."
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('description') }}</textarea>

                </div>


                {{-- Sort Order --}}
                <div>

                    <x-input-label
                        for="sort_order"
                        value="Sort Order"
                        class="uppercase text-xs tracking-wide" />


                    <x-text-input
                        id="sort_order"
                        name="sort_order"
                        type="number"
                        value="{{ old('sort_order', 0) }}"
                        min="0"
                        class="mt-1.5" />

                </div>


                {{-- Status --}}
                <div class="rounded-lg border border-app-border bg-surface-alt p-4">

                    <label class="flex cursor-pointer items-center justify-between gap-4">

                        <div>

                            <p class="text-sm font-medium text-secondary-dark">
                                Active Issue Type
                            </p>

                            <p class="mt-1 text-xs text-secondary">
                                Active issue types will be available to clients.
                            </p>

                        </div>


                        <input
                            type="hidden"
                            name="status"
                            value="0">


                        <input
                            type="checkbox"
                            name="status"
                            value="1"
                            @checked(old('status', true))
                            class="h-4 w-4 rounded border-app-border text-primary focus:ring-primary">

                    </label>

                </div>

            </div>


            {{-- Modal Footer --}}
            <div class="mt-8 flex justify-end gap-3">

                <x-secondary-button
                    type="button"
                    x-on:click="$dispatch('close')">

                    Cancel

                </x-secondary-button>


                <x-primary-button type="submit">

                    <x-icon
                        name="check"
                        class="h-4 w-4" />

                    Create Issue Type

                </x-primary-button>

            </div>

        </form>

    </x-modal>

</x-layout>