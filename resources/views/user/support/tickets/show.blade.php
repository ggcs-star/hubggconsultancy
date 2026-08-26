<x-layout title="Ticket #{{ $ticket->ticket_number }}">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div>

        <a
            href="{{ route('user.support.tickets.index') }}"
            class="inline-flex items-center gap-2 text-sm text-secondary transition hover:text-secondary-dark"
        >
            <x-icon
                name="arrow-left"
                class="h-4 w-4"
            />

            Back to My Tickets
        </a>


        @php

            /*
            |--------------------------------------------------------------------------
            | Status Classes
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | Status Label
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | Priority Classes
            |--------------------------------------------------------------------------
            */

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


        {{-- Header Information --}}
        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <div class="flex flex-wrap items-center gap-3">

                    <h1 class="text-2xl font-semibold text-secondary-dark">
                        Ticket #{{ $ticket->ticket_number }}
                    </h1>


                    <span
                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses }}"
                    >
                        {{ $statusLabel }}
                    </span>

                </div>


                {{-- Issue + Product --}}
                <div class="mt-2 flex flex-wrap items-center gap-2">

                    <p class="text-sm text-secondary">
                        {{ $ticket->issueType->name ?? 'Support Issue' }}
                    </p>


                    @if ($ticket->product)

                        <span class="text-secondary">
                            •
                        </span>

                        <p class="text-sm font-medium text-primary">
                            {{ $ticket->product->name }}
                        </p>

                    @endif

                </div>

            </div>

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
    {{-- VALIDATION ERRORS --}}
    {{-- ========================================================= --}}

    @if ($errors->any())

        <div class="mt-5 rounded-lg border border-danger/20 bg-danger-light px-4 py-3">

            <ul class="list-inside list-disc text-xs text-danger">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- MAIN CONTENT --}}
    {{-- ========================================================= --}}

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">


        {{-- ===================================================== --}}
        {{-- CONVERSATION --}}
        {{-- ===================================================== --}}

        <div class="lg:col-span-2">

            <div class="overflow-hidden rounded-xl border border-app-border bg-white">


                {{-- ================================================= --}}
                {{-- ORIGINAL ISSUE --}}
                {{-- ================================================= --}}

                <div class="border-b border-app-border p-5">

                    <div class="flex items-start gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">

                            <x-icon
                                name="{{ $ticket->issueType->icon ?? 'help-circle' }}"
                                class="h-5 w-5"
                            />

                        </div>


                        <div class="min-w-0">

                            <h2 class="font-semibold text-secondary-dark">
                                {{ $ticket->issueType->name ?? 'Support Issue' }}
                            </h2>


                            <p class="mt-1 text-xs text-secondary">

                                {{ $ticket->issueType?->module
                                    ? ucfirst($ticket->issueType->module)
                                    : 'Support' }}

                            </p>

                        </div>

                    </div>


                    {{-- Product --}}
                    @if ($ticket->product)

                        <div class="mt-4 flex items-center gap-3 rounded-lg border border-app-border bg-white p-3">

                            @if (!empty($ticket->product->logo))

                                <img
                                    src="{{ asset('storage/' . ltrim($ticket->product->logo, '/')) }}"
                                    alt="{{ $ticket->product->name }}"
                                    class="h-11 w-11 rounded-lg border border-app-border bg-white object-contain p-1"
                                >

                            @else

                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">

                                    <x-icon
                                        name="cube"
                                        class="h-5 w-5"
                                    />

                                </div>

                            @endif


                            <div class="min-w-0">

                                <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                                    Product
                                </p>

                                <p class="mt-0.5 truncate text-sm font-semibold text-secondary-dark">
                                    {{ $ticket->product->name }}
                                </p>


                                @if (!empty($ticket->product->category))

                                    <p class="mt-0.5 text-xs text-secondary">
                                        {{ $ticket->product->category }}
                                    </p>

                                @endif

                            </div>

                        </div>

                    @endif

                </div>


                {{-- ================================================= --}}
                {{-- CONVERSATION --}}
                {{-- ================================================= --}}

                <div class="space-y-6 p-5">


                    {{-- ================================================= --}}
                    {{-- MESSAGES --}}
                    {{-- ================================================= --}}
                    {{--
                        The user's initial message is already the first row in
                        $ticket->messages (created alongside the ticket in
                        store()), so it renders naturally via this loop — it is
                        NOT rendered again here separately, which used to show
                        the same "wq"-style description three times: once in
                        the "Original Description" box above, and twice more
                        (a hardcoded bubble plus the real looped message).
                    --}}

                    @foreach ($ticket->messages as $message)

                        @if ($message->sender_type === 'admin')

                            {{-- ========================================= --}}
                            {{-- ADMIN MESSAGE --}}
                            {{-- ========================================= --}}

                            <div class="flex items-start justify-end gap-3">

                                <div class="max-w-[80%]">

                                    <div class="flex items-center justify-end gap-2">

                                        <span class="text-xs text-secondary">
                                            {{ $message->created_at?->format('d M Y, h:i A') }}
                                        </span>


                                        <p class="text-sm font-semibold text-secondary-dark">
                                            {{ $message->user->name ?? 'Support Team' }}
                                        </p>

                                    </div>


                                    <div class="mt-2 rounded-lg rounded-tr-none bg-primary p-3">

                                        <p class="whitespace-pre-line text-sm leading-6 text-white">
                                            {{ $message->message }}
                                        </p>

                                    </div>


                                    {{-- Admin Attachment --}}
                                    @if ($message->attachment)

                                        <div class="mt-2 flex justify-end">

                                            <a
                                                href="{{ asset('storage/' . $message->attachment) }}"
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
                                            $message->user->name ?? 'S',
                                            0,
                                            1
                                        )
                                    ) }}

                                </div>

                            </div>


                        @else

                            {{-- ========================================= --}}
                            {{-- USER MESSAGE --}}
                            {{-- ========================================= --}}

                            <div class="flex items-start gap-3">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-light text-sm font-semibold text-primary">

                                    {{ strtoupper(
                                        substr(
                                            $message->user->name ?? auth()->user()->name,
                                            0,
                                            1
                                        )
                                    ) }}

                                </div>


                                <div class="min-w-0 flex-1">

                                    <div class="flex items-center gap-2">

                                        <p class="text-sm font-semibold text-secondary-dark">
                                            You
                                        </p>


                                        <span class="text-xs text-secondary">
                                            {{ $message->created_at?->format('d M Y, h:i A') }}
                                        </span>

                                    </div>


                                    <div class="mt-2 rounded-lg rounded-tl-none bg-surface-alt p-3">

                                        <p class="whitespace-pre-line text-sm leading-6 text-secondary-dark">
                                            {{ $message->message }}
                                        </p>

                                    </div>


                                    {{-- User Attachment --}}
                                    @if ($message->attachment)

                                        <div class="mt-2">

                                            <a
                                                href="{{ asset('storage/' . $message->attachment) }}"
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
                {{-- REPLY --}}
                {{-- ================================================= --}}

                @if (!in_array($ticket->status, ['resolved', 'closed']))

                    <div class="border-t border-app-border p-5">

                        <form
                            method="POST"
                            action="{{ route('user.support.tickets.reply', $ticket) }}"
                            enctype="multipart/form-data"
                        >

                            @csrf


                            <label
                                for="message"
                                class="block text-xs font-semibold uppercase tracking-wide text-secondary"
                            >
                                Reply
                            </label>


                            <textarea
                                id="message"
                                name="message"
                                rows="4"
                                required
                                placeholder="Write your reply..."
                                class="mt-1.5 w-full resize-none rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                            >{{ old('message') }}</textarea>


                            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                                <div>

                                    <label
                                        for="reply_attachment"
                                        class="inline-flex cursor-pointer items-center gap-2 text-sm font-medium text-secondary transition hover:text-secondary-dark"
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


                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90"
                                >

                                    <x-icon
                                        name="send"
                                        class="h-4 w-4"
                                    />

                                    Send Reply

                                </button>

                            </div>

                        </form>

                    </div>

                @else

                    <div class="border-t border-app-border bg-surface-alt p-5 text-center">

                        <p class="text-sm font-medium text-secondary-dark">
                            This ticket is {{ $statusLabel }}.
                        </p>


                        <p class="mt-1 text-xs text-secondary">
                            You cannot send a new reply to this ticket.
                        </p>

                    </div>

                @endif

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- RIGHT SIDEBAR --}}
        {{-- ========================================================= --}}

        <div class="space-y-6">


            {{-- ===================================================== --}}
            {{-- PRODUCT --}}
            {{-- ===================================================== --}}

            @if ($ticket->product)

                <div class="rounded-xl border border-app-border bg-white">

                    <div class="border-b border-app-border px-5 py-4">

                        <h2 class="font-semibold text-secondary-dark">
                            Product
                        </h2>

                    </div>


                    <div class="p-5">

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

                    </div>

                </div>

            @endif


            {{-- ===================================================== --}}
            {{-- TICKET INFORMATION --}}
            {{-- ===================================================== --}}

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


                    {{-- Issue --}}
                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-secondary">
                            Issue
                        </p>


                        <p class="mt-1 text-sm font-medium text-secondary-dark">
                            {{ $ticket->issueType->name ?? 'Support Issue' }}
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


            {{-- ===================================================== --}}
            {{-- TICKET ATTACHMENT --}}
            {{-- ===================================================== --}}

            @if ($ticket->attachment)

                <div class="rounded-xl border border-app-border bg-white">

                    <div class="border-b border-app-border px-5 py-4">

                        <h2 class="font-semibold text-secondary-dark">
                            Attachment
                        </h2>

                    </div>


                    <div class="p-5">

                        <div class="flex items-center gap-3">

                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">

                                <x-icon
                                    name="file-text"
                                    class="h-4 w-4"
                                />

                            </div>


                            <div class="min-w-0 flex-1">

                                <p class="truncate text-sm font-medium text-secondary-dark">
                                    {{ basename($ticket->attachment) }}
                                </p>


                                <p class="text-xs text-secondary">
                                    Support attachment
                                </p>

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

                </div>

            @endif

        </div>

    </div>

</x-layout>