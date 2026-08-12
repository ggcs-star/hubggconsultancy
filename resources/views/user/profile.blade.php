<x-layout title="My Profile" subtitle="Keep your details up to date">

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

    <div>
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="card p-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-700 text-xl font-bold text-white">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-800">{{ $user->name }}</p>
                        <p class="text-sm text-slate-400">{{ $user->email }}</p>
                    </div>
                </div>

                <h2 class="mt-8 text-base font-bold text-slate-800">Basic Information</h2>
                <p class="text-sm text-slate-400">Your name, contact details and education background</p>

                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="form-label">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Email Address</label>
                        <input type="email" value="{{ $user->email }}" disabled class="form-input bg-slate-50 text-slate-400">
                    </div>
                    <div>
                        <label for="phone" class="form-label">Phone Number</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input" placeholder="+91 90000 00000">
                    </div>
                    <div>
                        <label for="highest_qualification" class="form-label">Highest Qualification</label>
                        <input id="highest_qualification" type="text" name="highest_qualification" value="{{ old('highest_qualification', $user->highest_qualification) }}" class="form-input" placeholder="e.g. MBA">
                    </div>
                    <div>
                        <label for="institution_name" class="form-label">Institution Name</label>
                        <input id="institution_name" type="text" name="institution_name" value="{{ old('institution_name', $user->institution_name) }}" class="form-input" placeholder="e.g. Delhi University">
                    </div>
                    <div>
                        <label for="field_of_study" class="form-label">Field of Study</label>
                        <input id="field_of_study" type="text" name="field_of_study" value="{{ old('field_of_study', $user->field_of_study) }}" class="form-input" placeholder="e.g. Marketing">
                    </div>
                    <div>
                        <label for="education_year" class="form-label">Year of Completion</label>
                        <input id="education_year" type="text" name="education_year" value="{{ old('education_year', $user->education_year) }}" class="form-input" placeholder="e.g. 2022">
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h2 class="text-base font-bold text-slate-800">Address Details</h2>
                <p class="text-sm text-slate-400">Where you're based</p>

                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="address" class="form-label">Full Address</label>
                        <textarea id="address" name="address" rows="2" class="form-input" placeholder="House no., street, locality">{{ old('address', $user->address) }}</textarea>
                    </div>
                    <div>
                        <label for="pincode" class="form-label">Pincode</label>
                        <input id="pincode" type="text" name="pincode" value="{{ old('pincode', $user->pincode) }}" class="form-input" placeholder="400001">
                    </div>
                    <div>
                        <label for="city" class="form-label">City</label>
                        <input id="city" type="text" name="city" value="{{ old('city', $user->city) }}" class="form-input" placeholder="Mumbai">
                    </div>
                    <div>
                        <label for="state" class="form-label">State</label>
                        <input id="state" type="text" name="state" value="{{ old('state', $user->state) }}" class="form-input" placeholder="Maharashtra">
                    </div>
                    <div>
                        <label for="country" class="form-label">Country</label>
                        <input id="country" type="text" name="country" value="{{ old('country', $user->country) }}" class="form-input" placeholder="India">
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h2 class="text-base font-bold text-slate-800">Interest</h2>
                <p class="text-sm text-slate-400">Pick the SaaS products you're interested in learning to sell</p>

                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($saasProducts as $index => $product)
                        <label class="group relative block cursor-pointer">
                            <input
                                type="checkbox"
                                name="interests[]"
                                value="{{ $product->id }}"
                                class="peer sr-only"
                                @checked(in_array($product->id, old('interests', $interestIds)))
                            >
                            <div class="relative h-full rounded-xl border border-slate-200 p-4 transition peer-checked:border-brand-600 peer-checked:bg-brand-50 peer-checked:ring-1 peer-checked:ring-brand-600 peer-checked:[&_.interest-check]:flex hover:border-brand-300">
                                <span class="interest-check absolute right-3 top-3 hidden h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white">
                                    <x-icon name="check" class="h-3 w-3" />
                                </span>

                                @if ($product->logoUrl())
                                    <img src="{{ $product->logoUrl() }}" alt="" class="h-10 w-10 shrink-0 rounded-lg object-cover">
                                @else
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-sm font-bold {{ $accentColors[$index % count($accentColors)] }}">
                                        {{ strtoupper(substr($product->name, 0, 1)) }}
                                    </span>
                                @endif
                                <p class="mt-3 text-sm font-bold text-slate-800">{{ $product->name }}</p>
                                <div class="mt-1.5 flex flex-wrap gap-1.5">
                                    @if ($product->category)
                                        <span class="badge badge-slate">{{ $product->category }}</span>
                                    @endif
                                    @if ($product->emi_available)
                                        <span class="badge badge-green">EMI Available</span>
                                    @endif
                                </div>
                                <p class="mt-2 text-xs text-slate-500 line-clamp-3">{{ $product->description }}</p>
                            </div>
                        </label>
                    @empty
                        <p class="col-span-full text-sm text-slate-400">No products available yet.</p>
                    @endforelse
                </div>
            </div>

            <button type="submit" class="btn-primary w-full sm:w-auto">Save Profile</button>
        </form>
    </div>

</x-layout>
