<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password · GG Hub</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans lg:h-screen lg:overflow-hidden" x-data="{ showPassword: false, showPasswordConfirm: false }">

    <div class="flex min-h-screen items-stretch bg-white lg:h-screen lg:overflow-hidden">
        @include('partials.auth-side')

        <div class="flex w-full flex-col justify-center px-6 py-6 sm:px-12 lg:overflow-y-auto lg:py-8 xl:w-[42%] xl:px-16 lg:w-1/2">
            <div class="mx-auto w-full max-w-sm">
                <div class="mx-auto mb-4 flex h-28 w-28 items-center justify-center rounded-2xl border border-brand-100 bg-white p-3 shadow-sm">
                    <img src="{{ asset('favicon.png') }}" alt="Global Garner Hub" class="h-full w-full object-contain" />
                </div>

                <h2 class="text-center text-2xl font-extrabold text-slate-800">Reset Password</h2>
                <p class="mt-2 text-center text-sm text-slate-500">Choose a new password for your account.</p>

                @if ($errors->any())
                    <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label for="email" class="form-label">Email Address</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <x-icon name="mail" class="h-5 w-5" />
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus
                                   class="form-input pl-11" placeholder="you@example.com">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="form-label">New Password</label>
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
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <x-icon name="lock" class="h-5 w-5" />
                            </span>
                            <input :type="showPasswordConfirm ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required
                                   class="form-input pl-11 pr-11" placeholder="••••••••">
                            <button type="button" @click="showPasswordConfirm = !showPasswordConfirm"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600">
                                <x-icon name="eye" class="h-5 w-5" />
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full">Reset Password</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
