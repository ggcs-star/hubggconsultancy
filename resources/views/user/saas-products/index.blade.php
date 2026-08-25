<x-layout title="Products & Opportunities" title-icon="sparkles" subtitle="Browse the product catalog and mark what you're interested in selling">

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
        $blobColors = [
            'bg-orange-200',
            'bg-emerald-200',
            'bg-violet-200',
            'bg-amber-200',
            'bg-sky-200',
            'bg-fuchsia-200',
            'bg-rose-200',
            'bg-red-200',
            'bg-blue-200',
            'bg-indigo-200',
        ];
    @endphp

    <form method="GET" class="w-full sm:max-w-sm">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="form-input pl-10">
        </div>
    </form>

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($saasProducts as $index => $product)
            @php $interested = in_array($product->id, $interestIds); @endphp
            <div class="card relative flex flex-col overflow-hidden p-5">
                <div class="pointer-events-none absolute -bottom-8 -right-8 h-28 w-28 rounded-full opacity-25 blur-2xl {{ $blobColors[$index % count($blobColors)] }}"></div>

                <div class="relative flex flex-1 flex-col">
                    <div class="flex items-start justify-between">
                        @if ($product->logoUrl())
                            <img src="{{ $product->logoUrl() }}" alt="" class="h-11 w-11 shrink-0 rounded-xl object-cover">
                        @else
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-lg font-bold {{ $accentColors[$index % count($accentColors)] }}">
                                {{ strtoupper(substr($product->name, 0, 1)) }}
                            </span>
                        @endif

                        @if ($interested)
                            <span class="badge badge-green">
                                <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                Interested
                            </span>
                        @endif
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

                    <form method="POST" action="{{ route('user.saas-products.interest.toggle', $product) }}" class="mt-4 border-t border-slate-100 pt-3">
                        @csrf
                        <button type="submit" class="w-full rounded-xl border px-4 py-2 text-sm font-semibold transition {{ $interested ? 'border-slate-200 text-slate-500 hover:bg-slate-50' : 'border-brand-200 bg-white text-brand-700 hover:bg-brand-50' }}">
                            {{ $interested ? 'Remove Interest' : "I'm Interested" }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400">
                @if (request('search'))
                    No products match your search.
                @else
                    No products available yet — check back soon.
                @endif
            </div>
        @endforelse
    </div>

</x-layout>
