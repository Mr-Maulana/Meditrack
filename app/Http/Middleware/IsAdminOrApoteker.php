<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdminOrApoteker
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'apoteker'])) {
            return $next($request);
        }

        abort(403, 'Unauthorized. Admin or Apoteker only.');
    }
}