<x-layout title="My Profile" subtitle="Keep your details up to date">

    <div class="card mx-auto max-w-2xl p-6">
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-700 text-xl font-bold text-white">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <p class="font-bold text-slate-800">{{ $user->name }}</p>
                <p class="text-sm text-slate-400">{{ $user->email }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('user.profile.update') }}" class="mt-8 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="form-label">Full Name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input">
            </div>

            <div>
                <label class="form-label">Email Address</label>
                <input type="email" value="{{ $user->email }}" disabled class="form-input bg-slate-50 text-slate-400">
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="phone" class="form-label">Phone Number</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input" placeholder="+91 90000 00000">
                </div>
                <div>
                    <label for="city" class="form-label">City</label>
                    <input id="city" type="text" name="city" value="{{ old('city', $user->city) }}" class="form-input" placeholder="Mumbai">
                </div>
            </div>

            <button type="submit" class="btn-primary w-full sm:w-auto">Save Profile</button>
        </form>
    </div>

</x-layout>
