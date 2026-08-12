<x-layout title="My Support Tickets">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

        <div>

            <h1 class="text-2xl font-semibold text-secondary-dark">
                My Support Tickets
            </h1>

            <p class="mt-1 text-sm text-secondary">
                View and manage your support requests.
            </p>

        </div>


        <a
            href="{{ route('user.support.tickets.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90"
        >

            <x-icon
                name="plus"
                class="h-4 w-4"
            />

            Raise a Ticket

        </a>

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
    {{-- VALIDATION ERRORS --}}
    {{-- ========================================================= --}}

    @if ($errors->any())

        <div class="mt-5 rounded-lg border border-danger/20 bg-danger-light px-4 py-3 text-sm text-danger">

            <ul class="list-inside list-disc">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- STATS --}}
    {{-- ========================================================= --}}

    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">

        {{-- Total --}}
        <div class="rounded-xl border border-app-border bg-white p-4">

            <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                Total Tickets
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
    {{-- TICKETS --}}
    {{-- ========================================================= --}}

    <div class="mt-6 overflow-hidden rounded-xl border border-app-border bg-white">

        <div class="flex items-center justify-between border-b border-app-border px-5 py-4">

            <div>

                <h2 class="font-semibold text-secondary-dark">
                    My Tickets
                </h2>

                <p class="mt-1 text-xs text-secondary">
                    Your recently raised support requests
                </p>

            </div>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-app-border text-sm">

                <thead>

                    <tr class="bg-surface-alt text-left text-xs font-semibold uppercase tracking-wide text-secondary">

                        {{-- Ticket --}}
                        <th class="px-5 py-3">
                            Ticket
                        </th>


                        {{-- Product --}}
                        <th class="px-5 py-3">
                            Product
                        </th>


                        {{-- Issue --}}
                        <th class="px-5 py-3">
                            Issue
                        </th>


                        {{-- Priority --}}
                        <th class="px-5 py-3">
                            Priority
                        </th>


                        {{-- Status --}}
                        <th class="px-5 py-3">
                            Status
                        </th>


                        {{-- Created --}}
                        <th class="px-5 py-3">
                            Created
                        </th>


                        {{-- Action --}}
                        <th class="px-5 py-3 text-right">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-app-border">

                    @forelse ($tickets as $ticket)

                        <tr class="transition hover:bg-surface-alt">


                            {{-- ================================================= --}}
                            {{-- TICKET --}}
                            {{-- ================================================= --}}

                            <td class="px-5 py-4">

                                <a
                                    href="{{ route('user.support.tickets.show', $ticket) }}"
                                    class="font-semibold text-primary hover:underline"
                                >

                                    #{{ $ticket->ticket_number }}

                                </a>

                            </td>


                            {{-- ================================================= --}}
                            {{-- PRODUCT --}}
                            {{-- ================================================= --}}

                            <td class="px-5 py-4">

                                @if ($ticket->product)

                                    <div class="flex items-center gap-3">

                                        {{-- Product Logo --}}
                                        @if (!empty($ticket->product->logo))

                                            <img
                                                src="{{ asset('storage/' . ltrim($ticket->product->logo, '/')) }}"
                                                alt="{{ $ticket->product->name }}"
                                                class="h-9 w-9 rounded-lg border border-app-border object-contain bg-white p-1"
                                            >

                                        @else

                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">

                                                <x-icon
                                                    name="cube"
                                                    class="h-5 w-5"
                                                />

                                            </div>

                                        @endif


                                        <div class="min-w-0">

                                            <p
                                                class="max-w-[180px] truncate font-medium text-secondary-dark"
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

                            <td class="px-5 py-4">

                                <div>

                                    <p class="font-medium text-secondary-dark">

                                        {{ $ticket->issueType->name ?? 'Support Issue' }}

                                    </p>


                                    <p class="mt-0.5 text-xs text-secondary">

                                        {{ $ticket->issueType?->module
                                            ? ucfirst($ticket->issueType->module)
                                            : '—' }}

                                    </p>

                                </div>

                            </td>


                            {{-- ================================================= --}}
                            {{-- PRIORITY --}}
                            {{-- ================================================= --}}

                            <td class="px-5 py-4">

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
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $priorityClasses }}"
                                >

                                    {{ ucfirst($ticket->priority) }}

                                </span>

                            </td>


                            {{-- ================================================= --}}
                            {{-- STATUS --}}
                            {{-- ================================================= --}}

                            <td class="px-5 py-4">

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
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses }}"
                                >

                                    {{ $statusLabel }}

                                </span>

                            </td>


                            {{-- ================================================= --}}
                            {{-- CREATED --}}
                            {{-- ================================================= --}}

                            <td class="px-5 py-4 text-secondary">

                                {{ $ticket->created_at?->diffForHumans() }}

                            </td>


                            {{-- ================================================= --}}
                            {{-- ACTION --}}
                            {{-- ================================================= --}}

                            <td class="px-5 py-4 text-right">

                                <a
                                    href="{{ route('user.support.tickets.show', $ticket) }}"
                                    title="View Ticket"
                                    class="inline-flex items-center justify-center rounded-md border border-primary/30 bg-primary-light p-1.5 text-primary transition hover:border-primary/60"
                                >

                                    <x-icon
                                        name="eye"
                                        class="h-3.5 w-3.5"
                                    />

                                </a>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-5 py-12 text-center"
                            >

                                <div class="flex flex-col items-center">

                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-secondary">

                                        <x-icon
                                            name="inbox"
                                            class="h-6 w-6"
                                        />

                                    </div>


                                    <p class="mt-3 text-sm font-semibold text-secondary-dark">
                                        No support tickets yet
                                    </p>


                                    <p class="mt-1 text-xs text-secondary">
                                        You have not raised any support tickets.
                                    </p>


                                    <a
                                        href="{{ route('user.support.tickets.create') }}"
                                        class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline"
                                    >

                                        <x-icon
                                            name="plus"
                                            class="h-4 w-4"
                                        />

                                        Raise Your First Ticket

                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

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