<x-layout title="Raise Support Ticket">

    {{-- Header --}}
    <div>

        <a
            href="{{ route('user.support.tickets.index') }}"
            class="inline-flex items-center gap-2 text-sm text-secondary transition hover:text-secondary-dark"
        >
            <x-icon name="arrow-left" class="h-4 w-4" />
            Back to My Tickets
        </a>

        <h1 class="mt-4 text-2xl font-semibold text-secondary-dark">
            Raise a Support Ticket
        </h1>

        <p class="mt-1 text-sm text-secondary">
            Select the product and issue you are facing, then provide the required details.
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
            enctype="multipart/form-data"
        >

            @csrf


            <div class="p-6">


                {{-- Product --}}
                <div>

                    <label
                        for="product_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-secondary"
                    >
                        Product *

                    </label>


                    <select
                        id="product_id"
                        name="product_id"
                        required
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                    >

                        <option value="">
                            Select your product
                        </option>


                        @forelse ($products as $product)

                            <option
                                value="{{ $product->id }}"
                                @selected(old('product_id') == $product->id)
                            >
                                {{ $product->name }}

                                @if ($product->category)
                                    — {{ $product->category }}
                                @endif

                            </option>

                        @empty

                            <option
                                value=""
                                disabled
                            >
                                No products are assigned to your account
                            </option>

                        @endforelse

                    </select>


                    <p class="mt-1.5 text-xs text-secondary">
                        Select the product for which you need support.
                    </p>

                </div>


                {{-- Selected Product Information --}}
                <div
                    id="product-information"
                    class="mt-3 hidden rounded-lg border border-primary/20 bg-primary-light px-4 py-3"
                >

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-primary"
                        >
                            <x-icon
                                name="cube"
                                class="h-5 w-5"
                            />
                        </div>


                        <div>

                            <p class="text-xs font-semibold text-secondary-dark">
                                Selected Product
                            </p>

                            <p
                                id="product-information-text"
                                class="mt-1 text-xs leading-5 text-secondary"
                            ></p>

                        </div>

                    </div>

                </div>


                {{-- Issue Type --}}
                <div class="mt-6">

                    <label
                        for="issue_type_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-secondary"
                    >
                        What problem are you facing? *
                    </label>


                    <select
                        id="issue_type_id"
                        name="issue_type_id"
                        required
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                    >
                        <option value="">
                            Select a product first
                        </option>
                    </select>


                    <p class="mt-1.5 text-xs text-secondary">
                        Please select the option that best matches your problem.
                    </p>

                    <button
                        type="button"
                        id="add-issue-type-trigger"
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'quick-add-issue-type')"
                        disabled
                        class="mt-2 text-xs font-semibold text-primary hover:text-primary/80 disabled:cursor-not-allowed disabled:text-secondary disabled:opacity-60"
                    >
                        Can't find your issue? + Add one
                    </button>

                </div>


                {{-- Selected Issue Description --}}
                <div
                    id="issue-description"
                    class="mt-3 hidden rounded-lg border border-primary/20 bg-primary-light px-4 py-3"
                >

                    <p class="text-xs font-semibold text-secondary-dark">
                        About this issue
                    </p>

                    <p
                        id="issue-description-text"
                        class="mt-1 text-xs leading-5 text-secondary"
                    ></p>

                </div>


                {{-- Description --}}
                <div class="mt-6">

                    <label
                        for="description"
                        class="block text-xs font-semibold uppercase tracking-wide text-secondary"
                    >
                        Describe the Problem *
                    </label>


                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        required
                        placeholder="Please explain what is happening..."
                        class="mt-1.5 w-full resize-none rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                    >{{ old('description') }}</textarea>


                    <p class="mt-1.5 text-xs text-secondary">
                        Provide enough details so the support team can understand and resolve the issue.
                    </p>

                </div>


                {{-- Attachment --}}
                <div class="mt-6">

                    <label
                        for="attachment"
                        class="block text-xs font-semibold uppercase tracking-wide text-secondary"
                    >
                        Screenshot / Attachment
                    </label>


                    <div class="mt-1.5 rounded-xl border border-dashed border-app-border p-6">

                        <div class="text-center">

                            <div
                                class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-primary-light text-primary"
                            >

                                <x-icon
                                    name="upload"
                                    class="h-6 w-6"
                                />

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
                                class="mt-4 block w-full text-sm text-secondary"
                            >

                        </div>

                    </div>

                </div>


                {{-- Information --}}
                <div class="mt-6 rounded-xl border border-primary/20 bg-primary-light p-4">

                    <div class="flex items-start gap-3">

                        <x-icon
                            name="information-circle"
                            class="mt-0.5 h-5 w-5 shrink-0 text-primary"
                        />


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
                    class="inline-flex items-center justify-center rounded-lg border border-app-border bg-white px-4 py-2.5 text-sm font-medium text-secondary-dark transition hover:bg-surface-alt"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    @disabled($issueTypes->isEmpty() || $products->isEmpty())
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50"
                >

                    <x-icon
                        name="send"
                        class="h-4 w-4"
                    />

                    Submit Ticket

                </button>

            </div>

        </form>

    </div>


    {{-- Quick-add a new issue type under the selected product --}}
    <x-modal name="quick-add-issue-type" max-width="md">
        <form id="quick-add-issue-type-form" onsubmit="return submitQuickAddIssueType(event)">
            <div class="flex items-center justify-between border-b border-app-border px-6 py-4">
                <h2 class="text-lg font-semibold text-secondary-dark">Add an Issue</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <div class="space-y-3 px-6 py-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-secondary">Describe your issue in a few words</label>
                    <input type="text" id="quick-issue-type-name" required placeholder="e.g. Export button not working" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                </div>
                <p id="quick-issue-type-error" class="hidden text-sm text-danger"></p>
            </div>

            <div class="flex justify-end gap-3 border-t border-app-border px-6 py-4">
                <button type="button" x-on:click="$dispatch('close')" class="rounded-lg border border-app-border bg-white px-4 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">Cancel</button>
                <button type="submit" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary/90">Add Issue</button>
            </div>
        </form>
    </x-modal>


    {{-- Dynamic Product + Issue Information --}}
   <script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
        |--------------------------------------------------------------------------
        | Product Information
        |--------------------------------------------------------------------------
        */

        const productSelect = document.getElementById('product_id');
        const productInformation = document.getElementById('product-information');
        const productInformationText = document.getElementById('product-information-text');

        const products = {!! $products->mapWithKeys(function ($product) {
            return [
                $product->id => [
                    'name' => $product->name,
                    'category' => $product->category,
                ],
            ];
        })->toJson() !!};


        function updateProductInformation() {

            const productId = productSelect.value;
            const product = products[productId];

            if (product) {

                let text = product.name;

                if (product.category) {
                    text += ' — ' + product.category;
                }

                productInformationText.textContent = text;

                productInformation.classList.remove('hidden');

            } else {

                productInformationText.textContent = '';

                productInformation.classList.add('hidden');
            }
        }


        if (productSelect) {

            productSelect.addEventListener(
                'change',
                updateProductInformation
            );

            updateProductInformation();
        }


        /*
        |--------------------------------------------------------------------------
        | Issue Type Options — filtered by the selected product
        |--------------------------------------------------------------------------
        */

        const issueSelect = document.getElementById('issue_type_id');
        const issueDescription = document.getElementById('issue-description');
        const issueDescriptionText = document.getElementById('issue-description-text');

        const issuesByProduct = {!! $issueTypes->groupBy('saas_product_id')->map(function ($group) {
            return $group->map(fn ($issueType) => [
                'id' => $issueType->id,
                'name' => $issueType->name,
                'module' => $issueType->module,
                'description' => $issueType->description,
            ])->values();
        })->toJson() !!};

        const oldIssueTypeId = '{{ old('issue_type_id') }}';
        const addIssueTypeTrigger = document.getElementById('add-issue-type-trigger');

        function populateIssueOptions() {

            const productId = productSelect.value;
            const issues = issuesByProduct[productId] ?? [];

            addIssueTypeTrigger.disabled = !productId;

            issueSelect.innerHTML = '';

            if (!productId) {
                issueSelect.add(new Option('Select a product first', ''));
                updateIssueDescription();
                return;
            }

            if (issues.length === 0) {
                issueSelect.add(new Option('No support issues are currently available', '', true, true));
                updateIssueDescription();
                return;
            }

            issueSelect.add(new Option('Select an issue', ''));

            issues.forEach(function (issue) {
                const label = issue.module ? issue.name + ' — ' + issue.module.charAt(0).toUpperCase() + issue.module.slice(1) : issue.name;
                const isSelected = oldIssueTypeId !== '' && String(issue.id) === oldIssueTypeId;
                issueSelect.add(new Option(label, issue.id, isSelected, isSelected));
            });

            updateIssueDescription();
        }


        /*
        |--------------------------------------------------------------------------
        | Quick-add an Issue Type — "Can't find your issue?" popup
        |--------------------------------------------------------------------------
        */

        window.submitQuickAddIssueType = async function (event) {
            event.preventDefault();

            const nameInput = document.getElementById('quick-issue-type-name');
            const errorEl = document.getElementById('quick-issue-type-error');
            errorEl.classList.add('hidden');

            const productId = productSelect.value;

            if (!productId) {
                errorEl.textContent = 'Select a product first.';
                errorEl.classList.remove('hidden');
                return false;
            }

            try {
                const response = await fetch('{{ route('user.support.issue-types.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify({ saas_product_id: productId, name: nameInput.value }),
                });

                const data = await response.json();

                if (!response.ok) {
                    errorEl.textContent = data.errors?.name?.[0] ?? data.message ?? 'Could not add the issue.';
                    errorEl.classList.remove('hidden');
                    return false;
                }

                if (!issuesByProduct[productId]) {
                    issuesByProduct[productId] = [];
                }
                issuesByProduct[productId].push({ id: data.id, name: data.name, module: data.module, description: null });

                populateIssueOptions();
                issueSelect.value = String(data.id);
                updateIssueDescription();

                nameInput.value = '';
                window.dispatchEvent(new CustomEvent('close-modal', { detail: 'quick-add-issue-type' }));
            } catch (error) {
                errorEl.textContent = 'Something went wrong. Please try again.';
                errorEl.classList.remove('hidden');
            }

            return false;
        };


        function updateIssueDescription() {

            const productId = productSelect.value;
            const issueId = issueSelect.value;
            const issue = (issuesByProduct[productId] ?? []).find((item) => String(item.id) === issueId);

            if (issue && issue.description) {

                issueDescriptionText.textContent = issue.description;

                issueDescription.classList.remove('hidden');

            } else {

                issueDescriptionText.textContent = '';

                issueDescription.classList.add('hidden');
            }
        }


        if (issueSelect && productSelect) {

            productSelect.addEventListener('change', populateIssueOptions);
            issueSelect.addEventListener('change', updateIssueDescription);

            populateIssueOptions();
        }

    });
</script>

</x-layout>