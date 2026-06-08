<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsKurir
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (auth()->user()->isKurir() || auth()->user()->isAdmin())) {
            return $next($request);
        }

        abort(403, 'Unauthorized. Kurir or Admin only.');
    }
}