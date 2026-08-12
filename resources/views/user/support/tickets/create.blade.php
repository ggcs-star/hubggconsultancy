<x-layout title="Raise Support Ticket">

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


        <h1 class="mt-4 text-2xl font-semibold text-secondary-dark">
            Raise a Support Ticket
        </h1>


        <p class="mt-1 text-sm text-secondary">
            Select the issue you are facing and provide the required details.
        </p>

    </div>


    {{-- Validation Errors --}}
    @if ($errors->any())

        <div class="mt-5 rounded-lg border border-danger/20 bg-danger-light px-4 py-3">

            <p class="text-sm font-semibold text-danger">
                Please fix the following errors:
            </p>

            <ul class="mt-2 list-inside list-disc text-xs text-danger">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Form --}}
    <div class="mt-6 max-w-3xl rounded-xl border border-app-border bg-white">

        <form
            method="POST"
            action="{{ route('user.support.tickets.store') }}"
            enctype="multipart/form-data">

            @csrf


            <div class="p-6">


                {{-- Issue Type --}}
                <div>

                    <label
                        for="issue_type_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-secondary">

                        What problem are you facing? *

                    </label>


                    <select
                        id="issue_type_id"
                        name="issue_type_id"
                        required
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">

                        <option value="">
                            Select an issue
                        </option>


                        {{-- Dynamic Issue Types --}}
                        @forelse ($issueTypes as $issueType)

                            <option
                                value="{{ $issueType->id }}"
                                @selected(old('issue_type_id') == $issueType->id)>

                                {{ $issueType->name }}

                                @if ($issueType->module)
                                    — {{ ucfirst($issueType->module) }}
                                @endif

                            </option>

                        @empty

                            <option
                                value=""
                                disabled>

                                No support issues are currently available

                            </option>

                        @endforelse

                    </select>


                    <p class="mt-1.5 text-xs text-secondary">

                        Please select the option that best matches your problem.

                    </p>

                </div>


                {{-- Selected Issue Description --}}
                <div
                    id="issue-description"
                    class="mt-3 hidden rounded-lg border border-primary/20 bg-primary-light px-4 py-3">

                    <p class="text-xs font-semibold text-secondary-dark">
                        About this issue
                    </p>

                    <p
                        id="issue-description-text"
                        class="mt-1 text-xs leading-5 text-secondary">
                    </p>

                </div>


                {{-- Description --}}
                <div class="mt-6">

                    <label
                        for="description"
                        class="block text-xs font-semibold uppercase tracking-wide text-secondary">

                        Describe the Problem *

                    </label>


                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        required
                        placeholder="Please explain what is happening..."
                        class="mt-1.5 w-full resize-none rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('description') }}</textarea>


                    <p class="mt-1.5 text-xs text-secondary">

                        Provide enough details so the support team can understand and resolve the issue.

                    </p>

                </div>


                {{-- Attachment --}}
                <div class="mt-6">

                    <label
                        for="attachment"
                        class="block text-xs font-semibold uppercase tracking-wide text-secondary">

                        Screenshot / Attachment

                    </label>


                    <div class="mt-1.5 rounded-xl border border-dashed border-app-border p-6">

                        <div class="text-center">

                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-primary-light text-primary">

                                <x-icon
                                    name="upload"
                                    class="h-6 w-6" />

                            </div>


                            <p class="mt-3 text-sm font-medium text-secondary-dark">

                                Upload a screenshot or supporting file

                            </p>


                            <p class="mt-1 text-xs text-secondary">

                                PNG, JPG or PDF up to 10MB

                            </p>


                            <input
                                id="attachment"
                                type="file"
                                name="attachment"
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="mt-4 block w-full text-sm text-secondary">

                        </div>

                    </div>

                </div>


                {{-- Information --}}
                <div class="mt-6 rounded-xl border border-primary/20 bg-primary-light p-4">

                    <div class="flex items-start gap-3">

                        <x-icon
                            name="information-circle"
                            class="mt-0.5 h-5 w-5 shrink-0 text-primary" />


                        <div>

                            <p class="text-sm font-semibold text-secondary-dark">

                                How support works

                            </p>


                            <p class="mt-1 text-xs leading-5 text-secondary">

                                After submitting your ticket, our support team
                                will review the issue and respond to you here.

                            </p>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Footer --}}
            <div class="flex justify-end gap-3 border-t border-app-border px-6 py-4">

                <a
                    href="{{ route('user.support.tickets.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-app-border bg-white px-4 py-2.5 text-sm font-medium text-secondary-dark transition hover:bg-surface-alt">

                    Cancel

                </a>


                <button
                    type="submit"
                    @disabled($issueTypes->isEmpty())
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50">

                    <x-icon
                        name="send"
                        class="h-4 w-4" />

                    Submit Ticket

                </button>

            </div>

        </form>

    </div>


    {{-- Dynamic Issue Description --}}
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const issueSelect = document.getElementById('issue_type_id');
            const descriptionBox = document.getElementById('issue-description');
            const descriptionText = document.getElementById('issue-description-text');

            const issueDescriptions = @json(
                $issueTypes->mapWithKeys(function ($issueType) {
                    return [
                        $issueType->id => $issueType->description
                    ];
                })
            );


            function updateIssueDescription() {

                const issueId = issueSelect.value;

                const description = issueDescriptions[issueId];

                if (description) {

                    descriptionText.textContent = description;

                    descriptionBox.classList.remove('hidden');

                } else {

                    descriptionText.textContent = '';

                    descriptionBox.classList.add('hidden');

                }

            }


            issueSelect.addEventListener(
                'change',
                updateIssueDescription
            );


            updateIssueDescription();

        });

    </script>

</x-layout>