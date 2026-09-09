<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account · GG Hub</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans lg:h-screen lg:overflow-hidden" x-data="{ showPassword: false, showConfirm: false }">

    <div class="flex min-h-screen items-stretch bg-white lg:h-screen lg:overflow-hidden">
        @include('partials.auth-side')

        <div class="flex w-full flex-col justify-center px-6 py-6 sm:px-12 lg:overflow-y-auto lg:py-4 xl:w-[42%] xl:px-16 lg:w-1/2">
            <div class="mx-auto w-full max-w-sm">
                <div class="mx-auto mb-1.5 flex h-12 w-12 items-center justify-center rounded-2xl border border-brand-100 bg-white p-1.5 shadow-sm">
                    <img src="{{ asset('favicon.png') }}" alt="Global Garner Hub" class="h-full w-full object-contain" />
                </div>

                <h2 class="text-center text-2xl font-extrabold text-slate-800">Create Your Account</h2>
                <p class="mt-1 text-center text-sm text-slate-500">Join GG Hub and start your onboarding.</p>

                @if (session('status'))
                    <div class="mt-3 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="mt-2 space-y-2">
                    @csrf
                    <input type="hidden" name="referral_code" value="{{ old('referral_code', $referralCode ?? '') }}">

                    <div>
                        <label for="name" class="form-label">Full Name</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <x-icon name="user" class="h-5 w-5" />
                            </span>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                   class="form-input py-2.5 pl-11" placeholder="Jane Doe">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="form-label">Email Address</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <x-icon name="mail" class="h-5 w-5" />
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                   class="form-input py-2.5 pl-11" placeholder="you@example.com">
                        </div>
                    </div>

                    <div>
                        <label for="phone" class="form-label">Phone Number</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <x-icon name="phone" class="h-5 w-5" />
                            </span>
                            <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                                   class="form-input py-2.5 pl-11" placeholder="+91 90000 00000">
                        </div>
                        <p class="mt-0.5 text-xs text-slate-400">Enter at least one of email or phone number.</p>
                    </div>

                    <div>
                        <label for="password" class="form-label">Password</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <x-icon name="lock" class="h-5 w-5" />
                            </span>
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                                   class="form-input py-2.5 pl-11 pr-11" placeholder="••••••••">
                            <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600">
                                <x-icon name="eye" class="h-5 w-5" />
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <x-icon name="lock" class="h-5 w-5" />
                            </span>
                            <input :type="showConfirm ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required
                                   class="form-input py-2.5 pl-11 pr-11" placeholder="••••••••">
                            <button type="button" @click="showConfirm = !showConfirm"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600">
                                <x-icon name="eye" class="h-5 w-5" />
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full">Create Account</button>
                </form>

                <div class="mt-2 flex flex-col gap-3 rounded-xl border border-brand-200 bg-brand-50 px-4 py-2 sm:flex-row sm:items-center">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#25D366] text-white">
                            <x-brand-icon name="whatsapp" class="h-5 w-5" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-800">Trouble registering?</p>
                            <p class="text-xs text-brand-700">Chat with our support team on WhatsApp.</p>
                        </div>
                    </div>
                    <a href="https://api.whatsapp.com/send?phone=919662726192&text=Hello%20Team%20GG%2C%0A%0AI%20need%20assistance%20with%20registering%20on%20**GG%20Hub**.%20Kindly%20guide%20me%20through%20the%20registration%20process%20and%20help%20me%20with%20the%20next%20steps.%0A%0AThank%20you%20for%20your%20support."
                       target="_blank" rel="noopener"
                       class="inline-flex w-full shrink-0 items-center justify-center gap-1.5 rounded-xl bg-brand-700 px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-brand-800 sm:w-auto">
                        Chat Now
                    </a>
                </div>

                <p class="mt-2 text-center text-sm text-slate-500">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:text-brand-800">Log in</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
