<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\ViteManifestNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // A missing/stale build (e.g. mid-deploy, before `npm run build` finishes on the
        // server) must never surface as a raw crash to a live user — show a "we're
        // updating" holding page instead, which auto-refreshes until the build lands.
        //
        // @vite(...) always fails from *inside* a Blade view (layout.blade.php), and
        // Illuminate\View\Engines\CompilerEngine::handleViewException() wraps whatever
        // exception a view throws into a ViewException (chained via getPrevious()) before
        // it ever reaches here — so this can't type-hint ViteManifestNotFoundException
        // directly, it has to unwrap the chain to find it.
        $this->renderable(function (Throwable $e, Request $request) {
            if (! $this->causedByMissingViteManifest($e)) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'The site is being updated. Please try again shortly.'], 503);
            }

            return response()->view('errors.503', [], 503);
        });

        // A stale/expired CSRF token (e.g. a tab left open for a couple of days) should
        // never show as an error page — just send the user to log in again. Laravel's
        // own prepareException() converts TokenMismatchException into a generic
        // HttpException(419) before renderable callbacks run, so match on that instead
        // of the original class — and only for 419, so every other HttpException (404,
        // 403, etc.) still falls through to its normal errors/{code}.blade.php view.
        $this->renderable(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your session has expired. Please refresh and try again.'], 419);
            }

            return redirect()->route('login')->with('status', 'Your session has expired for your security. Please log in again.');
        });
    }

    /**
     * Walks the full getPrevious() chain, since view-rendering wraps the real
     * exception (ViteManifestNotFoundException) inside one or more ViewExceptions.
     */
    private function causedByMissingViteManifest(?Throwable $e): bool
    {
        while ($e !== null) {
            if ($e instanceof ViteManifestNotFoundException) {
                return true;
            }

            $e = $e->getPrevious();
        }

        return false;
    }
}
