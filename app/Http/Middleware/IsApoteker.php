<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsApoteker
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'apoteker') {
            return $next($request);
        }

        abort(403, 'Unauthorized. Apoteker only.');
    }
}