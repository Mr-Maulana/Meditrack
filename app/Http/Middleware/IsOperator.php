<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsOperator
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'apoteker', 'operator'])) {
            return $next($request);
        }

        abort(403, 'Unauthorized. Operator, Apoteker or Admin only.');
    }
}
