<x-layout title="Support Tickets">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

        <div>

            <h1 class="text-2xl font-semibold text-secondary-dark">
                Support Tickets
            </h1>

            <p class="mt-1 text-sm text-secondary">
                Manage and resolve support issues raised by clients.
            </p>

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

        <div class="mt-5 rounded-lg border border-danger/20 bg-danger-light px-4 py-3 text-sm text-danger">

            {{ session('error') }}

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- STATS --}}
    {{-- ========================================================= --}}

    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-5">

        {{-- Total --}}
        <div class="rounded-xl border border-app-border bg-white p-4">

            <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                Total
            </p>

            <p class="mt-2 text-2xl font-semibold text-secondary-dark">
                {{ $stats['total'] ?? 0 }}
            </p>

        </div>


        {{-- Open --}}
        <div class="rounded-xl border border-app-border bg-white p-4">

            <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                Open
            </p>

            <p class="mt-2 text-2xl font-semibold text-danger">
                {{ $stats['open'] ?? 0 }}
            </p>

        </div>


        {{-- In Progress --}}
        <div class="rounded-xl border border-app-border bg-white p-4">

            <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                In Progress
            </p>

            <p class="mt-2 text-2xl font-semibold text-warning">
                {{ $stats['in_progress'] ?? 0 }}
            </p>

        </div>


        {{-- Waiting --}}
        <div class="rounded-xl border border-app-border bg-white p-4">

            <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                Waiting
            </p>

            <p class="mt-2 text-2xl font-semibold text-chart-4">
                {{ $stats['waiting'] ?? 0 }}
            </p>

        </div>


        {{-- Resolved --}}
        <div class="rounded-xl border border-app-border bg-white p-4">

            <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                Resolved
            </p>

            <p class="mt-2 text-2xl font-semibold text-success">
                {{ $stats['resolved'] ?? 0 }}
            </p>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- FILTERS --}}
    {{-- ========================================================= --}}

    <form
        method="GET"
        action="{{ route('admin.support.tickets.index') }}"
        class="mt-6 rounded-xl border border-app-border bg-white p-4"
    >

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-12">


            {{-- Search --}}
            <div class="min-w-0 lg:col-span-4">

                <label
                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary"
                >
                    Search Ticket
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Ticket ID, user name, email, product..."
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                >

            </div>


            {{-- Status --}}
            <div class="min-w-0 lg:col-span-3">

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
                        All Statuses
                    </option>

                    <option
                        value="open"
                        @selected(request('status') === 'open')
                    >
                        Open
                    </option>

                    <option
                        value="in_progress"
                        @selected(request('status') === 'in_progress')
                    >
                        In Progress
                    </option>

                    <option
                        value="waiting_for_user"
                        @selected(request('status') === 'waiting_for_user')
                    >
                        Waiting for User
                    </option>

                    <option
                        value="resolved"
                        @selected(request('status') === 'resolved')
                    >
                        Resolved
                    </option>

                    <option
                        value="closed"
                        @selected(request('status') === 'closed')
                    >
                        Closed
                    </option>

                </select>

            </div>


            {{-- Priority --}}
            <div class="min-w-0 lg:col-span-3">

                <label
                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary"
                >
                    Priority
                </label>

                <select
                    name="priority"
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                >

                    <option value="">
                        All Priorities
                    </option>

                    <option
                        value="urgent"
                        @selected(request('priority') === 'urgent')
                    >
                        Urgent
                    </option>

                    <option
                        value="high"
                        @selected(request('priority') === 'high')
                    >
                        High
                    </option>

                    <option
                        value="medium"
                        @selected(request('priority') === 'medium')
                    >
                        Medium
                    </option>

                    <option
                        value="low"
                        @selected(request('priority') === 'low')
                    >
                        Low
                    </option>

                </select>

            </div>


            {{-- Filter --}}
            <div class="flex items-end lg:col-span-2">

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary/90"
                >

                    <x-icon
                        name="filter"
                        class="h-4 w-4"
                    />

                    Filter

                </button>

            </div>

        </div>

    </form>


    {{-- ========================================================= --}}
    {{-- TICKETS TABLE --}}
    {{-- ========================================================= --}}

    <div class="mt-6 overflow-x-auto rounded-xl border border-app-border bg-white">

        <table class="min-w-full divide-y divide-app-border text-sm">

            <thead>

                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">

                    <th class="px-4 py-3">
                        Ticket
                    </th>

                    <th class="px-4 py-3">
                        Client
                    </th>

                    <th class="px-4 py-3">
                        Product
                    </th>

                    <th class="px-4 py-3">
                        Issue
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

                    <th class="px-4 py-3">
                        Created
                    </th>

                    <th class="px-4 py-3 text-right">
                        Action
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-app-border">

                @forelse ($tickets as $ticket)

                    <tr class="hover:bg-surface-alt">


                        {{-- ================================================= --}}
                        {{-- TICKET --}}
                        {{-- ================================================= --}}

                        <td class="px-4 py-3">

                            <a
                                href="{{ route('admin.support.tickets.show', $ticket) }}"
                                class="font-semibold text-primary hover:underline"
                            >

                                #{{ $ticket->ticket_number }}

                            </a>

                        </td>


                        {{-- ================================================= --}}
                        {{-- CLIENT --}}
                        {{-- ================================================= --}}

                        <td class="px-4 py-3">

                            <div>

                                <p class="font-medium text-secondary-dark">

                                    {{ $ticket->user->name ?? 'Unknown User' }}

                                </p>


                                <p class="text-xs text-secondary">

                                    {{ $ticket->user->email ?? '—' }}

                                </p>

                            </div>

                        </td>


                        {{-- ================================================= --}}
                        {{-- PRODUCT --}}
                        {{-- ================================================= --}}

                        <td class="px-4 py-3">

                            @if ($ticket->product)

                                <div class="flex items-center gap-2.5">

                                    {{-- Product Logo --}}
                                    @if (!empty($ticket->product->logo))

                                        <img
                                            src="{{ asset('storage/' . ltrim($ticket->product->logo, '/')) }}"
                                            alt="{{ $ticket->product->name }}"
                                            class="h-9 w-9 rounded-lg border border-app-border bg-white object-contain p-1"
                                        >

                                    @else

                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">

                                            <x-icon
                                                name="cube"
                                                class="h-4 w-4"
                                            />

                                        </div>

                                    @endif


                                    <div class="min-w-0">

                                        <p
                                            class="max-w-[160px] truncate font-medium text-secondary-dark"
                                            title="{{ $ticket->product->name }}"
                                        >
                                            {{ $ticket->product->name }}
                                        </p>


                                        @if (!empty($ticket->product->category))

                                            <p class="mt-0.5 text-xs text-secondary">
                                                {{ $ticket->product->category }}
                                            </p>

                                        @endif

                                    </div>

                                </div>

                            @else

                                <span class="text-xs text-secondary">
                                    Product unavailable
                                </span>

                            @endif

                        </td>


                        {{-- ================================================= --}}
                        {{-- ISSUE --}}
                        {{-- ================================================= --}}

                        <td class="px-4 py-3">

                            <div class="flex items-center gap-2">

                                <span
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary"
                                >

                                    <x-icon
                                        name="{{ $ticket->issueType->icon ?? 'help-circle' }}"
                                        class="h-4 w-4"
                                    />

                                </span>


                                <span class="font-medium text-secondary-dark">

                                    {{ $ticket->issueType->name ?? 'Unknown Issue' }}

                                </span>

                            </div>

                        </td>


                        {{-- ================================================= --}}
                        {{-- MODULE --}}
                        {{-- ================================================= --}}

                        <td class="px-4 py-3 text-secondary-dark">

                            @if ($ticket->issueType?->module)

                                {{ ucfirst($ticket->issueType->module) }}

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

                                $priorityClasses = match ($ticket->priority) {

                                    'urgent',
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

                                {{ ucfirst($ticket->priority) }}

                            </span>

                        </td>


                        {{-- ================================================= --}}
                        {{-- STATUS --}}
                        {{-- ================================================= --}}

                        <td class="px-4 py-3">

                            @php

                                $statusClasses = match ($ticket->status) {

                                    'open' =>
                                        'bg-danger-light text-danger',

                                    'in_progress' =>
                                        'bg-warning-light text-warning',

                                    'waiting_for_user' =>
                                        'bg-chart-4/15 text-chart-4',

                                    'resolved' =>
                                        'bg-success-light text-success',

                                    'closed' =>
                                        'bg-slate-100 text-slate-600',

                                    default =>
                                        'bg-slate-100 text-slate-600',

                                };


                                $statusLabel = match ($ticket->status) {

                                    'open' =>
                                        'Open',

                                    'in_progress' =>
                                        'In Progress',

                                    'waiting_for_user' =>
                                        'Waiting for User',

                                    'resolved' =>
                                        'Resolved',

                                    'closed' =>
                                        'Closed',

                                    default =>
                                        ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $ticket->status
                                            )
                                        ),

                                };

                            @endphp


                            <span
                                class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses }}"
                            >

                                {{ $statusLabel }}

                            </span>

                        </td>


                        {{-- ================================================= --}}
                        {{-- CREATED --}}
                        {{-- ================================================= --}}

                        <td class="px-4 py-3 text-secondary">

                            {{ $ticket->created_at?->diffForHumans() }}

                        </td>


                        {{-- ================================================= --}}
                        {{-- ACTION --}}
                        {{-- ================================================= --}}

                        <td class="px-4 py-3">

                            <div class="flex justify-end">

                                <a
                                    href="{{ route('admin.support.tickets.show', $ticket) }}"
                                    title="View Ticket"
                                    class="inline-flex items-center justify-center rounded-md border border-primary/30 bg-primary-light p-1.5 text-primary hover:border-primary/60"
                                >

                                    <x-icon
                                        name="eye"
                                        class="h-3.5 w-3.5"
                                    />

                                </a>

                            </div>

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="9"
                            class="px-4 py-12 text-center"
                        >

                            <div class="flex flex-col items-center">

                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-secondary">

                                    <x-icon
                                        name="inbox"
                                        class="h-6 w-6"
                                    />

                                </div>


                                <p class="mt-3 text-sm font-semibold text-secondary-dark">
                                    No support tickets found
                                </p>


                                <p class="mt-1 text-xs text-secondary">
                                    Tickets raised by clients will appear here.
                                </p>

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

    @if ($tickets->hasPages())

        <div class="mt-5">

            {{ $tickets->links() }}

        </div>

    @endif

</x-layout>