<x-layout title="Ticket #{{ $ticket->ticket_number }}">

    {{-- Header --}}
    <div>

        <a
            href="{{ route('user.support.tickets.index') }}"
            class="inline-flex items-center gap-2 text-sm text-secondary transition hover:text-secondary-dark">

            <x-icon
                name="arrow-left"
                class="h-4 w-4" />

            Back to My Tickets

        </a>


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

                'open' => 'Open',

                'in_progress' => 'In Progress',

                'waiting_for_user' => 'Waiting for User',

                'resolved' => 'Resolved',

                'closed' => 'Closed',

                default =>
                    ucfirst(
                        str_replace(
                            '_',
                            ' ',
                            $ticket->status
                        )
                    ),

            };


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


        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <div class="flex flex-wrap items-center gap-3">

                    <h1 class="text-2xl font-semibold text-secondary-dark">

                        Ticket #{{ $ticket->ticket_number }}

                    </h1>


                    <span
                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses }}">

                        {{ $statusLabel }}

                    </span>

                </div>


                <p class="mt-1 text-sm text-secondary">

                    {{ $ticket->issueType->name ?? 'Support Issue' }}

                </p>

            </div>

        </div>

    </div>


    {{-- Success --}}
    @if (session('success'))

        <div class="mt-5 rounded-lg border border-success/20 bg-success-light px-4 py-3 text-sm text-success">

            {{ session('success') }}

        </div>

    @endif


    {{-- Error --}}
    @if (session('error'))

        <div class="mt-5 rounded-lg border border-danger/20 bg-danger-light px-4 py-3 text-sm text-danger">

            {{ session('error') }}

        </div>

    @endif


    {{-- Validation Errors --}}
    @if ($errors->any())

        <div class="mt-5 rounded-lg border border-danger/20 bg-danger-light px-4 py-3">

            <ul class="list-inside list-disc text-xs text-danger">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Main Content --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">


        {{-- Conversation --}}
        <div class="lg:col-span-2">

            <div class="overflow-hidden rounded-xl border border-app-border bg-white">


                {{-- Original Issue --}}
                <div class="border-b border-app-border p-5">

                    <div class="flex items-start gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">

                            <x-icon
                                name="{{ $ticket->issueType->icon ?? 'help-circle' }}"
                                class="h-5 w-5" />

                        </div>


                        <div>

                            <h2 class="font-semibold text-secondary-dark">

                                {{ $ticket->issueType->name ?? 'Support Issue' }}

                            </h2>


                            <p class="mt-1 text-xs text-secondary">

                                {{ $ticket->issueType->module
                                    ? ucfirst($ticket->issueType->module)
                                    : 'Support' }}

                            </p>

                        </div>

                    </div>


                    <div class="mt-4 rounded-lg bg-surface-alt p-4">

                        <p class="whitespace-pre-line text-sm leading-6 text-secondary-dark">

                            {{ $ticket->description }}

                        </p>

                    </div>

                </div>


                {{-- Conversation --}}
                <div class="space-y-6 p-5">


                    {{-- Initial Ticket Message --}}
                    <div class="flex items-start gap-3">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-light text-sm font-semibold text-primary">

                            {{ strtoupper(
                                substr(
                                    auth()->user()->name,
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

                                    {{ $ticket->created_at?->format('d M Y, h:i A') }}

                                </span>

                            </div>


                            <div class="mt-2 rounded-lg rounded-tl-none bg-surface-alt p-3">

                                <p class="whitespace-pre-line text-sm leading-6 text-secondary-dark">

                                    {{ $ticket->description }}

                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- Messages --}}
                    @foreach ($ticket->messages as $message)

                        @if ($message->sender_type === 'admin')

                            {{-- Admin Message --}}
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
                                                class="inline-flex items-center gap-2 text-xs font-medium text-primary hover:underline">

                                                <x-icon
                                                    name="paperclip"
                                                    class="h-3.5 w-3.5" />

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

                            {{-- User Message --}}
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
                                                class="inline-flex items-center gap-2 text-xs font-medium text-primary hover:underline">

                                                <x-icon
                                                    name="paperclip"
                                                    class="h-3.5 w-3.5" />

                                                View Attachment

                                            </a>

                                        </div>

                                    @endif

                                </div>

                            </div>

                        @endif

                    @endforeach

                </div>


                {{-- Reply --}}
                @if (!in_array($ticket->status, ['resolved', 'closed']))

                    <div class="border-t border-app-border p-5">

                        <form
                            method="POST"
                            action="{{ route('user.support.tickets.reply', $ticket) }}"
                            enctype="multipart/form-data">

                            @csrf


                            <label
                                for="message"
                                class="block text-xs font-semibold uppercase tracking-wide text-secondary">

                                Reply

                            </label>


                            <textarea
                                id="message"
                                name="message"
                                rows="4"
                                required
                                placeholder="Write your reply..."
                                class="mt-1.5 w-full resize-none rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"></textarea>


                            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">


                                <div>

                                    <label
                                        for="reply_attachment"
                                        class="inline-flex cursor-pointer items-center gap-2 text-sm font-medium text-secondary transition hover:text-secondary-dark">

                                        <x-icon
                                            name="paperclip"
                                            class="h-4 w-4" />

                                        Attach File

                                    </label>


                                    <input
                                        id="reply_attachment"
                                        type="file"
                                        name="attachment"
                                        accept=".jpg,.jpeg,.png,.pdf"
                                        class="hidden">


                                    <p class="mt-1 text-xs text-secondary">

                                        JPG, PNG or PDF up to 10MB

                                    </p>

                                </div>


                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90">

                                    <x-icon
                                        name="send"
                                        class="h-4 w-4" />

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


        {{-- Right Sidebar --}}
        <div class="space-y-6">


            {{-- Ticket Information --}}
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

                            {{ $ticket->issueType->module
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
                            class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $priorityClasses }}">

                            {{ ucfirst($ticket->priority) }}

                        </span>

                    </div>


                    {{-- Status --}}
                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-secondary">

                            Status

                        </p>


                        <span
                            class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses }}">

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


            {{-- Ticket Attachment --}}
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
                                    class="h-4 w-4" />

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
                                class="text-sm font-medium text-primary hover:underline">

                                View

                            </a>

                        </div>

                    </div>

                </div>

            @endif

        </div>

    </div>

</x-layout>