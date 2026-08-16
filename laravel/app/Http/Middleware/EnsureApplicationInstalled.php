<?php

namespace App\Http\Middleware;

use App\Services\InstallationState;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicationInstalled
{
    public function __construct(private readonly InstallationState $state)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->state->isInstalled()) {
            return redirect()->route('setup.index');
        }

        return $next($request);
    }
}
