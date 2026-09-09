<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In · GG Hub</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans lg:h-screen lg:overflow-hidden" x-data="{ showPassword: false }">

    <div class="flex min-h-screen items-stretch bg-white lg:h-screen lg:overflow-hidden">
        @include('partials.auth-side')

        <div class="flex w-full flex-col justify-center px-6 py-6 sm:px-12 lg:overflow-y-auto lg:py-8 xl:w-[42%] xl:px-16 lg:w-1/2">
            <div class="mx-auto w-full max-w-sm">
                <div class="mx-auto mb-4 flex h-28 w-28 items-center justify-center rounded-2xl border border-brand-100 bg-white p-3 shadow-sm">
                    <img src="{{ asset('favicon.png') }}" alt="Global Garner Hub" class="h-full w-full object-contain" />
                </div>

                <h2 class="text-center text-2xl font-extrabold text-slate-800">Welcome Back!</h2>
                <p class="mt-2 text-center text-sm text-slate-500">Please sign in to continue to your account.</p>

                @if (session('status'))
                    <div class="mt-5 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="login" class="form-label">Email or Phone Number</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <x-icon name="mail" class="h-5 w-5" />
                            </span>
                            <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus
                                   class="form-input pl-11" placeholder="you@example.com or phone number">
                        </div>
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

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 text-slate-500">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-700 focus:ring-brand-500">
                            Remember me
                        </label>
                        <a href="#" class="font-semibold text-brand-700 hover:text-brand-800">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-primary w-full">Log In</button>
                </form>

                <div class="mt-5 flex flex-col gap-3 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3.5 sm:flex-row sm:items-center">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#25D366] text-white">
                            <x-brand-icon name="whatsapp" class="h-5 w-5" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-800">Trouble logging in?</p>
                            <p class="text-xs text-brand-700">Chat with our support team on WhatsApp.</p>
                        </div>
                    </div>
                    <a href="https://api.whatsapp.com/send?phone=919662726192&text=Hello%20Team%20GG%2C%0A%0AI%20need%20assistance%20with%20logging%20in%20to%20**GG%20Hub**.%20Kindly%20guide%20me%20through%20the%20login%20process%20and%20help%20me%20with%20the%20next%20steps.%0A%0AThank%20you%20for%20your%20support."
                       target="_blank" rel="noopener"
                       class="inline-flex w-full shrink-0 items-center justify-center gap-1.5 rounded-xl bg-brand-700 px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-brand-800 sm:w-auto">
                        Chat Now
                    </a>
                </div>

                <p class="mt-6 text-center text-sm text-slate-500">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold text-brand-700 hover:text-brand-800">Sign up</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
