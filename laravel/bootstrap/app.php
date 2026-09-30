<?php

use App\Http\Middleware\EnsureApplicationInstalled;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['installed' => EnsureApplicationInstalled::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*') || $request->expectsJson());
        $exceptions->render(function (HttpException $exception, Request $request) {
            if (! $exception->getPrevious() instanceof TokenMismatchException
                || ! $request->hasSession() || $request->user()) {
                return null;
            }

            $message = 'Your session expired. Please sign in again.';

            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => $message, 'redirect' => route('login')], 419);
            }

            // Never replay the rejected submission or flash its credentials/input.
            return redirect()->route('login')->with('status', $message);
        });
    })->create();
