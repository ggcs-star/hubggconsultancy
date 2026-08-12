<x-layout title="Ticket #{{ $ticket->ticket_number }}">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

        <div>

            <div class="flex items-center gap-3">

                <a
                    href="{{ route('admin.support.tickets.index') }}"
                    class="text-secondary hover:text-secondary-dark"
                >

                    <x-icon
                        name="arrow-left"
                        class="h-5 w-5"
                    />

                </a>


                <h1 class="text-2xl font-semibold text-secondary-dark">

                    Ticket #{{ $ticket->ticket_number }}

                </h1>


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

            </div>


            {{-- Issue + Client + Product --}}
            <div class="mt-1 ml-8 flex flex-wrap items-center gap-2 text-sm">

                <span class="text-secondary">
                    {{ $ticket->issueType->name ?? 'Support Issue' }}
                </span>


                <span class="text-secondary">
                    •
                </span>


                <span class="text-secondary">
                    raised by
                </span>


                <span class="font-medium text-secondary-dark">
                    {{ $ticket->user->name ?? 'Unknown User' }}
                </span>


                @if ($ticket->product)

                    <span class="text-secondary">
                        •
                    </span>


                    <span class="font-medium text-primary">

                        {{ $ticket->product->name }}

                    </span>

                @endif

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- QUICK STATUS --}}
        {{-- ===================================================== --}}

        <div class="flex flex-wrap items-center gap-2">

            @if ($ticket->status !== 'in_progress')

                <form
                    method="POST"
                    action="{{ route('admin.support.tickets.status', $ticket) }}"
                >

                    @csrf

                    @method('PATCH')


                    <input
                        type="hidden"
                        name="status"
                        value="in_progress"
                    >


                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-lg border border-warning/30 bg-warning-light px-4 py-2.5 text-sm font-medium text-warning hover:bg-warning/20"
                    >

                        <x-icon
                            name="clock"
                            class="h-4 w-4"
                        />

                        Mark In Progress

                    </button>

                </form>

            @endif


            @if (!in_array($ticket->status, ['resolved', 'closed']))

                <form
                    method="POST"
                    action="{{ route('admin.support.tickets.status', $ticket) }}"
                >

                    @csrf

                    @method('PATCH')


                    <input
                        type="hidden"
                        name="status"
                        value="resolved"
                    >


                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-success px-4 py-2.5 text-sm font-medium text-white hover:bg-success/90"
                    >

                        <x-icon
                            name="check-circle"
                            class="h-4 w-4"
                        />

                        Resolve Ticket

                    </button>

                </form>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SUCCESS --}}
    {{-- ========================================================= --}}

    @if (session('success'))

        <div class="mt-5 rounded-lg border border-success/20 bg-success-light px-4 py-3 text-sm text-success">

            {{ session('success') }}

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- ERROR --}}
    {{-- ========================================================= --}}

    @if (session('error'))

        <div class="mt-5 rounded-lg border border-danger/20 bg-danger-light px-4 py-3 text-sm text-danger">

            {{ session('error') }}

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- MAIN GRID --}}
    {{-- ========================================================= --}}

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">


        {{-- ===================================================== --}}
        {{-- LEFT --}}
        {{-- ===================================================== --}}

        <div class="space-y-6 lg:col-span-2">


            {{-- ================================================= --}}
            {{-- ISSUE DETAILS --}}
            {{-- ================================================= --}}

            <div class="rounded-xl border border-app-border bg-white">

                <div class="border-b border-app-border px-5 py-4">

                    <h2 class="font-semibold text-secondary-dark">
                        Issue Details
                    </h2>

                </div>


                <div class="p-5">


                    {{-- Issue --}}
                    <div class="flex items-start gap-4">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">

                            <x-icon
                                name="{{ $ticket->issueType->icon ?? 'help-circle' }}"
                                class="h-5 w-5"
                            />

                        </div>


                        <div class="min-w-0">

                            <h3 class="text-lg font-semibold text-secondary-dark">

                                {{ $ticket->issueType->name ?? 'Support Issue' }}

                            </h3>


                            <p class="mt-1 text-sm text-secondary">

                                {{ $ticket->issueType->description ?? '' }}

                            </p>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- PRODUCT --}}
                    {{-- ================================================= --}}

                    @if ($ticket->product)

                        <div class="mt-5 rounded-xl border border-primary/20 bg-primary-light/30 p-4">

                            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-secondary">
                                Product
                            </p>


                            <div class="flex items-center gap-3">

                                {{-- Product Logo --}}
                                @if (!empty($ticket->product->logo))

                                    <img
                                        src="{{ asset('storage/' . ltrim($ticket->product->logo, '/')) }}"
                                        alt="{{ $ticket->product->name }}"
                                        class="h-12 w-12 rounded-xl border border-app-border bg-white object-contain p-1"
                                    >

                                @else

                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-light text-primary">

                                        <x-icon
                                            name="cube"
                                            class="h-6 w-6"
                                        />

                                    </div>

                                @endif


                                <div class="min-w-0">

                                    <p class="text-sm font-semibold text-secondary-dark">
                                        {{ $ticket->product->name }}
                                    </p>


                                    @if (!empty($ticket->product->category))

                                        <p class="mt-0.5 text-xs text-secondary">
                                            {{ $ticket->product->category }}
                                        </p>

                                    @endif

                                </div>

                            </div>

                        </div>

                    @else

                        <div class="mt-5 rounded-lg border border-app-border bg-surface-alt p-3">

                            <p class="text-xs text-secondary">
                                No product is associated with this ticket.
                            </p>

                        </div>

                    @endif


                    {{-- ================================================= --}}
                    {{-- USER DESCRIPTION --}}
                    {{-- ================================================= --}}

                    <div class="mt-6">

                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-secondary">

                            User Description

                        </p>


                        <div class="rounded-lg border border-app-border bg-surface-alt p-4">

                            <p class="whitespace-pre-line text-sm leading-6 text-secondary-dark">

                                {{ $ticket->description }}

                            </p>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- ATTACHMENT --}}
                    {{-- ================================================= --}}

                    @if (!empty($ticket->attachment))

                        <div class="mt-6">

                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-secondary">

                                Attachment

                            </p>


                            <div class="flex items-center justify-between rounded-lg border border-app-border p-3">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-light text-primary">

                                        <x-icon
                                            name="file-text"
                                            class="h-4 w-4"
                                        />

                                    </div>


                                    <div>

                                        <p class="text-sm font-medium text-secondary-dark">

                                            {{ basename($ticket->attachment) }}

                                        </p>


                                        <p class="text-xs text-secondary">
                                            Support attachment
                                        </p>

                                    </div>

                                </div>


                                <a
                                    href="{{ asset('storage/' . $ticket->attachment) }}"
                                    target="_blank"
                                    class="text-sm font-medium text-primary hover:underline"
                                >

                                    View

                                </a>

                            </div>

                        </div>

                    @endif

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- CONVERSATION --}}
            {{-- ================================================= --}}

            <div class="rounded-xl border border-app-border bg-white">

                <div class="border-b border-app-border px-5 py-4">

                    <div class="flex items-center justify-between">

                        <h2 class="font-semibold text-secondary-dark">
                            Conversation
                        </h2>


                        <span class="text-xs text-secondary">

                            {{ $ticket->messages->count() + 1 }}

                            messages

                        </span>

                    </div>

                </div>


                <div class="space-y-5 p-5">


                    {{-- ================================================= --}}
                    {{-- ORIGINAL USER MESSAGE --}}
                    {{-- ================================================= --}}

                    <div class="flex items-start gap-3">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-light text-sm font-semibold text-primary">

                            {{ strtoupper(
                                substr(
                                    $ticket->user->name ?? 'U',
                                    0,
                                    1
                                )
                            ) }}

                        </div>


                        <div class="min-w-0 flex-1">

                            <div class="flex items-center gap-2">

                                <p class="text-sm font-semibold text-secondary-dark">

                                    {{ $ticket->user->name ?? 'User' }}

                                </p>


                                <span class="text-xs text-secondary">

                                    {{ $ticket->created_at?->format('d M Y, h:i A') }}

                                </span>

                            </div>


                            <div class="mt-2 rounded-lg rounded-tl-none bg-surface-alt p-3">

                                <p class="whitespace-pre-line text-sm leading-6 text-secondary-dark">

                                    {{ $ticket->description }}

                                </p>

                            </div>


                            {{-- Initial Attachment --}}
                            @if (!empty($ticket->attachment))

                                <div class="mt-2">

                                    <a
                                        href="{{ asset('storage/' . $ticket->attachment) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 text-xs font-medium text-primary hover:underline"
                                    >

                                        <x-icon
                                            name="paperclip"
                                            class="h-3.5 w-3.5"
                                        />

                                        View Attachment

                                    </a>

                                </div>

                            @endif

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- REPLIES --}}
                    {{-- ================================================= --}}

                    @foreach ($ticket->messages as $reply)

                        @if ($reply->sender_type === 'admin')

                            {{-- Admin Reply --}}
                            <div class="flex items-start justify-end gap-3">

                                <div class="max-w-[80%]">

                                    <div class="flex items-center justify-end gap-2">

                                        <span class="text-xs text-secondary">

                                            {{ $reply->created_at?->format('d M Y, h:i A') }}

                                        </span>


                                        <p class="text-sm font-semibold text-secondary-dark">

                                            {{ $reply->user->name ?? 'Admin' }}

                                        </p>

                                    </div>


                                    <div class="mt-2 rounded-lg rounded-tr-none bg-primary p-3">

                                        <p class="whitespace-pre-line text-sm leading-6 text-white">

                                            {{ $reply->message }}

                                        </p>

                                    </div>


                                    {{-- Admin Attachment --}}
                                    @if (!empty($reply->attachment))

                                        <div class="mt-2 flex justify-end">

                                            <a
                                                href="{{ asset('storage/' . $reply->attachment) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-2 text-xs font-medium text-primary hover:underline"
                                            >

                                                <x-icon
                                                    name="paperclip"
                                                    class="h-3.5 w-3.5"
                                                />

                                                View Attachment

                                            </a>

                                        </div>

                                    @endif

                                </div>


                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-semibold text-white">

                                    {{ strtoupper(
                                        substr(
                                            $reply->user->name ?? 'A',
                                            0,
                                            1
                                        )
                                    ) }}

                                </div>

                            </div>

                        @else

                            {{-- User Reply --}}
                            <div class="flex items-start gap-3">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-light text-sm font-semibold text-primary">

                                    {{ strtoupper(
                                        substr(
                                            $reply->user->name ?? 'U',
                                            0,
                                            1
                                        )
                                    ) }}

                                </div>


                                <div class="min-w-0 flex-1">

                                    <div class="flex items-center gap-2">

                                        <p class="text-sm font-semibold text-secondary-dark">

                                            {{ $reply->user->name ?? 'User' }}

                                        </p>


                                        <span class="text-xs text-secondary">

                                            {{ $reply->created_at?->format('d M Y, h:i A') }}

                                        </span>

                                    </div>


                                    <div class="mt-2 rounded-lg rounded-tl-none bg-surface-alt p-3">

                                        <p class="whitespace-pre-line text-sm leading-6 text-secondary-dark">

                                            {{ $reply->message }}

                                        </p>

                                    </div>


                                    {{-- User Attachment --}}
                                    @if (!empty($reply->attachment))

                                        <div class="mt-2">

                                            <a
                                                href="{{ asset('storage/' . $reply->attachment) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-2 text-xs font-medium text-primary hover:underline"
                                            >

                                                <x-icon
                                                    name="paperclip"
                                                    class="h-3.5 w-3.5"
                                                />

                                                View Attachment

                                            </a>

                                        </div>

                                    @endif

                                </div>

                            </div>

                        @endif

                    @endforeach

                </div>


                {{-- ================================================= --}}
                {{-- ADMIN REPLY --}}
                {{-- ================================================= --}}

                @if (!in_array($ticket->status, ['resolved', 'closed']))

                    <div class="border-t border-app-border p-5">

                        <form
                            method="POST"
                            action="{{ route('admin.support.tickets.reply', $ticket) }}"
                            enctype="multipart/form-data"
                        >

                            @csrf


                            <label
                                for="message"
                                class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary"
                            >

                                Reply to Client

                            </label>


                            <textarea
                                id="message"
                                name="message"
                                rows="4"
                                required
                                placeholder="Write your response..."
                                class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                            ></textarea>


                            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                                <div>

                                    <label
                                        for="reply_attachment"
                                        class="inline-flex cursor-pointer items-center gap-2 text-sm font-medium text-secondary hover:text-secondary-dark"
                                    >

                                        <x-icon
                                            name="paperclip"
                                            class="h-4 w-4"
                                        />

                                        Attach File

                                    </label>


                                    <input
                                        id="reply_attachment"
                                        type="file"
                                        name="attachment"
                                        accept=".jpg,.jpeg,.png,.pdf"
                                        class="hidden"
                                    >


                                    <p class="mt-1 text-xs text-secondary">
                                        JPG, PNG or PDF up to 10MB
                                    </p>

                                </div>


                                <x-primary-button type="submit">

                                    <x-icon
                                        name="send"
                                        class="h-4 w-4"
                                    />

                                    Send Reply

                                </x-primary-button>

                            </div>

                        </form>

                    </div>

                @else

                    <div class="border-t border-app-border bg-surface-alt p-4 text-center">

                        <p class="text-sm text-secondary">

                            This ticket is {{ $statusLabel }} and cannot receive new replies.

                        </p>

                    </div>

                @endif

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- RIGHT SIDEBAR --}}
        {{-- ===================================================== --}}

        <div class="space-y-6">


            {{-- ================================================= --}}
            {{-- CLIENT INFORMATION --}}
            {{-- ================================================= --}}

            <div class="rounded-xl border border-app-border bg-white">

                <div class="border-b border-app-border px-5 py-4">

                    <h2 class="font-semibold text-secondary-dark">
                        Client Information
                    </h2>

                </div>


                <div class="p-5">

                    <div class="flex items-center gap-3">

                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-primary-light font-semibold text-primary">

                            {{ strtoupper(
                                substr(
                                    $ticket->user->name ?? 'U',
                                    0,
                                    1
                                )
                            ) }}

                        </div>


                        <div class="min-w-0">

                            <p class="truncate font-medium text-secondary-dark">

                                {{ $ticket->user->name ?? 'Unknown User' }}

                            </p>


                            <p class="truncate text-xs text-secondary">

                                {{ $ticket->user->email ?? '—' }}

                            </p>

                        </div>

                    </div>


                    <div class="mt-5">

                        <a
                            href="{{ route('admin.clients') }}"
                            class="text-sm font-medium text-primary hover:underline"
                        >

                            View Client

                        </a>

                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- PRODUCT INFORMATION --}}
            {{-- ================================================= --}}

            <div class="rounded-xl border border-app-border bg-white">

                <div class="border-b border-app-border px-5 py-4">

                    <h2 class="font-semibold text-secondary-dark">
                        Product Information
                    </h2>

                </div>


                <div class="p-5">

                    @if ($ticket->product)

                        <div class="flex items-center gap-3">

                            {{-- Product Logo --}}
                            @if (!empty($ticket->product->logo))

                                <img
                                    src="{{ asset('storage/' . ltrim($ticket->product->logo, '/')) }}"
                                    alt="{{ $ticket->product->name }}"
                                    class="h-12 w-12 rounded-xl border border-app-border bg-white object-contain p-1"
                                >

                            @else

                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-light text-primary">

                                    <x-icon
                                        name="cube"
                                        class="h-6 w-6"
                                    />

                                </div>

                            @endif


                            <div class="min-w-0">

                                <p class="text-sm font-semibold text-secondary-dark">

                                    {{ $ticket->product->name }}

                                </p>


                                @if (!empty($ticket->product->category))

                                    <p class="mt-1 text-xs text-secondary">

                                        {{ $ticket->product->category }}

                                    </p>

                                @endif

                            </div>

                        </div>


                        {{-- Product ID --}}
                        @if (!empty($ticket->product->id))

                            <div class="mt-4">

                                <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                                    Product ID
                                </p>

                                <p class="mt-1 text-sm text-secondary-dark">
                                    #{{ $ticket->product->id }}
                                </p>

                            </div>

                        @endif

                    @else

                        <div class="rounded-lg bg-surface-alt p-3">

                            <p class="text-sm text-secondary">
                                Product unavailable
                            </p>

                        </div>

                    @endif

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- TICKET INFORMATION --}}
            {{-- ================================================= --}}

            <div class="rounded-xl border border-app-border bg-white">

                <div class="border-b border-app-border px-5 py-4">

                    <h2 class="font-semibold text-secondary-dark">
                        Ticket Information
                    </h2>

                </div>


                <div class="space-y-4 p-5">


                    {{-- Ticket ID --}}
                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                            Ticket ID
                        </p>


                        <p class="mt-1 text-sm font-medium text-secondary-dark">

                            #{{ $ticket->ticket_number }}

                        </p>

                    </div>


                    {{-- Product --}}
                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                            Product
                        </p>


                        <p class="mt-1 text-sm font-medium text-secondary-dark">

                            {{ $ticket->product->name ?? 'Product unavailable' }}

                        </p>

                    </div>


                    {{-- Issue Type --}}
                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                            Issue Type
                        </p>


                        <p class="mt-1 text-sm font-medium text-secondary-dark">

                            {{ $ticket->issueType->name ?? '—' }}

                        </p>

                    </div>


                    {{-- Module --}}
                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                            Module
                        </p>


                        <p class="mt-1 text-sm font-medium text-secondary-dark">

                            {{ $ticket->issueType?->module
                                ? ucfirst($ticket->issueType->module)
                                : '—' }}

                        </p>

                    </div>


                    {{-- Priority --}}
                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                            Priority
                        </p>


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
                            class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $priorityClasses }}"
                        >

                            {{ ucfirst($ticket->priority) }}

                        </span>

                    </div>


                    {{-- Status --}}
                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                            Status
                        </p>


                        <span
                            class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses }}"
                        >

                            {{ $statusLabel }}

                        </span>

                    </div>


                    {{-- Created --}}
                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                            Created
                        </p>


                        <p class="mt-1 text-sm text-secondary-dark">

                            {{ $ticket->created_at?->format('d M Y, h:i A') }}

                        </p>

                    </div>


                    {{-- Resolved --}}
                    @if ($ticket->resolved_at)

                        <div>

                            <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                                Resolved
                            </p>


                            <p class="mt-1 text-sm text-secondary-dark">

                                {{ $ticket->resolved_at->format('d M Y, h:i A') }}

                            </p>

                        </div>

                    @endif

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- UPDATE STATUS --}}
            {{-- ================================================= --}}

            <div class="rounded-xl border border-app-border bg-white">

                <div class="border-b border-app-border px-5 py-4">

                    <h2 class="font-semibold text-secondary-dark">
                        Update Status
                    </h2>

                </div>


                <div class="p-5">

                    <form
                        method="POST"
                        action="{{ route('admin.support.tickets.status', $ticket) }}"
                    >

                        @csrf

                        @method('PATCH')


                        <select
                            name="status"
                            class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                        >

                            <option
                                value="open"
                                @selected($ticket->status === 'open')
                            >
                                Open
                            </option>


                            <option
                                value="in_progress"
                                @selected($ticket->status === 'in_progress')
                            >
                                In Progress
                            </option>


                            <option
                                value="waiting_for_user"
                                @selected($ticket->status === 'waiting_for_user')
                            >
                                Waiting for User
                            </option>


                            <option
                                value="resolved"
                                @selected($ticket->status === 'resolved')
                            >
                                Resolved
                            </option>


                            <option
                                value="closed"
                                @selected($ticket->status === 'closed')
                            >
                                Closed
                            </option>

                        </select>


                        <button
                            type="submit"
                            class="mt-3 w-full rounded-lg border border-app-border bg-white px-4 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt"
                        >

                            Update Status

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</x-layout>