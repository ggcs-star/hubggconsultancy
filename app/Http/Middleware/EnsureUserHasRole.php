<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'You do not have access to this area.');
        }

        if (! in_array($user->role, $roles, true)) {
            return $this->logoutAndRedirect($request, 'Please log in with an account that has access to this area.');
        }

        if ($user->isBlocked()) {
            return $this->logoutAndRedirect($request, 'Your account has been blocked. Please contact your administrator.');
        }

        return $next($request);
    }

    private function logoutAndRedirect(Request $request, string $message): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors(['email' => $message]);
    }
}
