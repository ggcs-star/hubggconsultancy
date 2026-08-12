<x-layout title="Add Support Issue Type">

    {{-- Header --}}
    <div class="flex flex-col gap-1">

        <h1 class="text-2xl font-semibold text-secondary-dark">
            Add Support Issue Type
        </h1>

        <p class="text-sm text-secondary">
            Create a product-specific issue that clients can select when raising a support ticket.
        </p>

    </div>


    {{-- Form --}}
    <div class="mt-6 max-w-3xl rounded-xl border border-app-border bg-white">

        <form
            method="POST"
            action="{{ route('admin.support.issue-types.store') }}"
        >

            @csrf

            <div class="p-6">

                {{-- Validation Errors --}}
                @if ($errors->any())

                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">

                        <p class="text-sm font-semibold text-red-700">
                            Please fix the following errors:
                        </p>

                        <ul class="mt-2 list-disc pl-5 text-sm text-red-600">

                            @foreach ($errors->all() as $error)

                                <li>
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                @endif


                {{-- ================================================= --}}
                {{-- SaaS Product --}}
                {{-- ================================================= --}}

                <div>

                    <x-input-label
                        for="saas_product_id"
                        value="SaaS Product *"
                        class="uppercase text-xs tracking-wide"
                    />

                    <select
                        id="saas_product_id"
                        name="saas_product_id"
                        required
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                    >

                        <option value="">
                            Select SaaS Product
                        </option>

                        @foreach ($products as $product)

                            <option
                                value="{{ $product->id }}"
                                @selected(
                                    old('saas_product_id') == $product->id
                                )
                            >
                                {{ $product->name }}
                            </option>

                        @endforeach

                    </select>

                    <p class="mt-1.5 text-xs text-secondary">
                        Select the SaaS product for which this issue type applies.
                    </p>

                </div>


                {{-- ================================================= --}}
                {{-- Issue Name --}}
                {{-- ================================================= --}}

                <div class="mt-5">

                    <x-input-label
                        for="name"
                        value="Issue Name *"
                        class="uppercase text-xs tracking-wide"
                    />

                    <x-text-input
                        id="name"
                        name="name"
                        class="mt-1.5"
                        placeholder="e.g. Payment Error"
                        value="{{ old('name') }}"
                        required
                    />

                    <p class="mt-1.5 text-xs text-secondary">
                        This name will be visible to clients.
                    </p>

                </div>


                {{-- ================================================= --}}
                {{-- Module + Priority --}}
                {{-- ================================================= --}}

                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">

                    {{-- Module --}}
                    <div>

                        <x-input-label
                            for="module"
                            value="Module"
                            class="uppercase text-xs tracking-wide"
                        />

                        <select
                            id="module"
                            name="module"
                            class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                        >

                            <option value="">
                                Select Module
                            </option>

                            <option
                                value="dashboard"
                                @selected(old('module') === 'dashboard')
                            >
                                Dashboard
                            </option>

                            <option
                                value="payment"
                                @selected(old('module') === 'payment')
                            >
                                Payment
                            </option>

                            <option
                                value="products"
                                @selected(old('module') === 'products')
                            >
                                Products
                            </option>

                            <option
                                value="system"
                                @selected(old('module') === 'system')
                            >
                                System
                            </option>

                            <option
                                value="website"
                                @selected(old('module') === 'website')
                            >
                                Website
                            </option>

                            <option
                                value="api"
                                @selected(old('module') === 'api')
                            >
                                API
                            </option>

                        </select>

                    </div>


                    {{-- Priority --}}
                    <div>

                        <x-input-label
                            for="default_priority"
                            value="Default Priority"
                            class="uppercase text-xs tracking-wide"
                        />

                        <select
                            id="default_priority"
                            name="default_priority"
                            required
                            class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                        >

                            <option
                                value="low"
                                @selected(
                                    old('default_priority', 'medium') === 'low'
                                )
                            >
                                Low
                            </option>

                            <option
                                value="medium"
                                @selected(
                                    old('default_priority', 'medium') === 'medium'
                                )
                            >
                                Medium
                            </option>

                            <option
                                value="high"
                                @selected(
                                    old('default_priority', 'medium') === 'high'
                                )
                            >
                                High
                            </option>

                            <option
                                value="urgent"
                                @selected(
                                    old('default_priority', 'medium') === 'urgent'
                                )
                            >
                                Urgent
                            </option>

                        </select>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- Icon --}}
                {{-- ================================================= --}}

                <div class="mt-5">

                    <x-input-label
                        for="icon"
                        value="Icon"
                        class="uppercase text-xs tracking-wide"
                    />

                    <x-text-input
                        id="icon"
                        name="icon"
                        class="mt-1.5"
                        placeholder="e.g. credit-card, image, clock"
                        value="{{ old('icon') }}"
                    />

                    <p class="mt-1.5 text-xs text-secondary">
                        Enter the icon name supported by your icon component.
                    </p>

                </div>


                {{-- ================================================= --}}
                {{-- Description --}}
                {{-- ================================================= --}}

                <div class="mt-5">

                    <x-input-label
                        for="description"
                        value="Description"
                        class="uppercase text-xs tracking-wide"
                    />

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Explain what this issue type means..."
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                    >{{ old('description') }}</textarea>

                </div>


                {{-- ================================================= --}}
                {{-- Sort Order --}}
                {{-- ================================================= --}}

                <div class="mt-5">

                    <x-input-label
                        for="sort_order"
                        value="Sort Order"
                        class="uppercase text-xs tracking-wide"
                    />

                    <x-text-input
                        id="sort_order"
                        name="sort_order"
                        type="number"
                        min="0"
                        value="{{ old('sort_order', 0) }}"
                        class="mt-1.5"
                    />

                    <p class="mt-1.5 text-xs text-secondary">
                        Lower numbers will appear first in the client ticket form.
                    </p>

                </div>


                {{-- ================================================= --}}
                {{-- Status --}}
                {{-- ================================================= --}}

                <div class="mt-6 rounded-lg border border-app-border bg-surface-alt p-4">

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
                            value="0"
                        />

                        <input
                            type="checkbox"
                            name="status"
                            value="1"
                            @checked(old('status', true))
                            class="h-4 w-4 rounded border-app-border text-primary focus:ring-primary"
                        />

                    </label>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- Footer --}}
            {{-- ================================================= --}}

            <div class="flex justify-end gap-3 border-t border-app-border px-6 py-4">

                <a
                    href="{{ route('admin.support.issue-types.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-app-border bg-white px-4 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt"
                >
                    Cancel
                </a>


                <x-primary-button type="submit">

                    <x-icon
                        name="check"
                        class="h-4 w-4"
                    />

                    Create Issue Type

                </x-primary-button>

            </div>

        </form>

    </div>

</x-layout>