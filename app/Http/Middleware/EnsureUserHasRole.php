<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Controleer of de ingelogde gebruiker de vereiste rol heeft.
     * Gebruik: Route::middleware('role:admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            abort(403, __('Geen toegang. Je hebt niet de juiste rechten voor deze pagina.'));
        }

        return $next($request);
    }
}
