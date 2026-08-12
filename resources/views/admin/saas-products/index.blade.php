<x-layout title="SaaS Products" subtitle="The product catalog salespeople and clients can express interest in">

    @php
        $accentColors = [
            'bg-orange-50 text-orange-600',
            'bg-emerald-50 text-emerald-600',
            'bg-violet-50 text-violet-600',
            'bg-amber-50 text-amber-600',
            'bg-sky-50 text-sky-600',
            'bg-fuchsia-50 text-fuchsia-600',
            'bg-rose-50 text-rose-600',
            'bg-red-50 text-red-600',
            'bg-blue-50 text-blue-600',
            'bg-indigo-50 text-indigo-600',
        ];
    @endphp

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="w-full sm:max-w-sm">
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="form-input pl-10">
            </div>
        </form>

        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-product')" class="btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Add Product
        </button>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($saasProducts as $index => $product)
            <div class="card flex flex-col p-5">
                <div class="flex items-start justify-between">
                    @if ($product->logoUrl())
                        <img src="{{ $product->logoUrl() }}" alt="" class="h-11 w-11 shrink-0 rounded-xl object-cover">
                    @else
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-lg font-bold {{ $accentColors[$index % count($accentColors)] }}">
                            {{ strtoupper(substr($product->name, 0, 1)) }}
                        </span>
                    @endif

                    <div class="flex items-center gap-1.5">
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-product-{{ $product->id }}')" title="Edit" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                            <x-icon name="pencil" class="h-4 w-4" />
                        </button>
                        <form method="POST" action="{{ route('admin.saas-products.destroy', $product) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete {{ $product->name }}? This removes it from everyone\'s interests too.', target: $el })">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                </div>

                <p class="mt-3 font-bold text-slate-800">{{ $product->name }}</p>

                <div class="mt-2 flex flex-wrap gap-1.5">
                    @if ($product->category)
                        <span class="badge badge-slate">{{ $product->category }}</span>
                    @endif
                    @if ($product->emi_available)
                        <span class="badge badge-green">EMI Available</span>
                    @endif
                </div>

                <p class="mt-3 flex-1 text-sm text-slate-500 line-clamp-3">{{ $product->description }}</p>

                <form method="POST" action="{{ route('admin.saas-products.toggle-status', $product) }}" class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="active" value="{{ $product->active ? '0' : '1' }}">
                    <span class="text-xs font-medium text-slate-400">{{ $product->active ? 'Visible to salespeople' : 'Hidden' }}</span>
                    <button type="submit" class="badge {{ $product->active ? 'badge-green' : 'badge-slate' }}">
                        {{ $product->active ? 'Active' : 'Inactive' }}
                    </button>
                </form>
            </div>

            <x-modal name="edit-product-{{ $product->id }}" :show="false" max-width="lg">
                <form method="POST" action="{{ route('admin.saas-products.update', $product) }}" class="p-6" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-800">Edit Product</h2>
                        <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
                            <x-icon name="x" class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="mt-6 space-y-5">
                        <div>
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" value="{{ $product->name }}" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Category</label>
                            <input type="text" name="category" value="{{ $product->category }}" class="form-input" placeholder="e.g. Real Estate">
                        </div>
                        <div>
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3" class="form-input">{{ $product->description }}</textarea>
                        </div>
                        <div>
                            <label class="form-label">Logo</label>
                            <input type="file" name="logo" accept="image/*" class="form-input">
                        </div>
                        <label class="flex items-center gap-2 text-sm font-medium text-slate-600">
                            <input type="checkbox" name="emi_available" value="1" @checked($product->emi_available) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            EMI Available
                        </label>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="btn-primary">Save Changes</button>
                    </div>
                </form>
            </x-modal>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400">
                No products yet. Click "Add Product" to create the first one.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $saasProducts->links() }}
    </div>

    <x-modal name="add-product" :show="$errors->isNotEmpty()" max-width="lg">
        <form method="POST" action="{{ route('admin.saas-products.store') }}" class="p-6" enctype="multipart/form-data">
            @csrf

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-800">Add Product</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <div class="mt-6 space-y-5">
                <div>
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-input" placeholder="e.g. Rapid Retail">
                </div>
                <div>
                    <label class="form-label">Category</label>
                    <input type="text" name="category" value="{{ old('category') }}" class="form-input" placeholder="e.g. Quick Commerce">
                </div>
                <div>
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-input">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="form-label">Logo</label>
                    <input type="file" name="logo" accept="image/*" class="form-input">
                </div>
                <label class="flex items-center gap-2 text-sm font-medium text-slate-600">
                    <input type="checkbox" name="emi_available" value="1" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    EMI Available
                </label>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="btn-primary">Create Product</button>
            </div>
        </form>
    </x-modal>

</x-layout>
