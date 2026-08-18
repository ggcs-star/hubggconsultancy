<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In · Pre Sales School</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen overflow-hidden font-sans" x-data="{ showPassword: false }">

    <div class="flex h-screen items-stretch overflow-hidden bg-white">
        @include('partials.auth-side')

        <div class="flex w-full flex-col justify-center overflow-y-auto px-6 py-8 sm:px-12 lg:w-1/2 xl:w-[42%] xl:px-16">
            <div class="mx-auto w-full max-w-sm">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-700">
                    <x-icon name="lock" class="h-7 w-7" />
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
                        <label for="email" class="form-label">Email Address</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <x-icon name="mail" class="h-5 w-5" />
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="form-input pl-11" placeholder="you@example.com">
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

                <div class="my-5 flex items-center gap-3">
                    <div class="h-px flex-1 bg-slate-200"></div>
                    <span class="text-xs font-medium uppercase tracking-wider text-slate-400">Or</span>
                    <div class="h-px flex-1 bg-slate-200"></div>
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
