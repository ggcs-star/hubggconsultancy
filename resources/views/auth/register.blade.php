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
<body class="h-screen overflow-hidden font-sans" x-data="{ showPassword: false, showConfirm: false }">

    <div class="flex h-screen items-stretch overflow-hidden bg-white">
        @include('partials.auth-side')

        <div class="flex w-full flex-col justify-center overflow-y-auto px-6 py-8 sm:px-12 lg:w-1/2 xl:w-[42%] xl:px-16">
            <div class="mx-auto w-full max-w-sm">
                <div class="mx-auto mb-4 flex h-28 w-28 items-center justify-center rounded-2xl border border-brand-100 bg-white p-3 shadow-sm">
                    <img src="{{ asset('favicon.png') }}" alt="Global Garner Hub" class="h-full w-full object-contain" />
                </div>

                <h2 class="text-center text-2xl font-extrabold text-slate-800">Create Your Account</h2>
                <p class="mt-2 text-center text-sm text-slate-500">Join GG Hub and start your onboarding.</p>

                @if (session('status'))
                    <div class="mt-5 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
                    @csrf
                    <input type="hidden" name="referral_code" value="{{ old('referral_code', $referralCode ?? '') }}">

                    <div>
                        <label for="name" class="form-label">Full Name</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <x-icon name="user" class="h-5 w-5" />
                            </span>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                   class="form-input pl-11" placeholder="Jane Doe">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="form-label">Email Address</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <x-icon name="mail" class="h-5 w-5" />
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                   class="form-input pl-11" placeholder="you@example.com">
                        </div>
                    </div>

                    <div>
                        <label for="phone" class="form-label">Phone Number</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <x-icon name="phone" class="h-5 w-5" />
                            </span>
                            <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                                   class="form-input pl-11" placeholder="+91 90000 00000">
                        </div>
                        <p class="mt-1 text-xs text-slate-400">Enter at least one of email or phone number.</p>
                    </div>

                    <div>
                        <label for="password" class="form-label">Password</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <x-icon name="lock" class="h-5 w-5" />
                            </span>
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                                   class="form-input pl-11 pr-11" placeholder="••••••••">
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
                                   class="form-input pl-11 pr-11" placeholder="••••••••">
                            <button type="button" @click="showConfirm = !showConfirm"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600">
                                <x-icon name="eye" class="h-5 w-5" />
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full">Create Account</button>
                </form>

                <div class="my-5 flex items-center gap-3">
                    <div class="h-px flex-1 bg-slate-200"></div>
                    <span class="text-xs font-medium uppercase tracking-wider text-slate-400">Or</span>
                    <div class="h-px flex-1 bg-slate-200"></div>
                </div>

                <a href="{{ route('auth.google') }}"
                   class="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    <x-google-icon class="h-5 w-5" />
                    Continue with Google
                </a>

                <p class="mt-6 text-center text-sm text-slate-500">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:text-brand-800">Log in</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
