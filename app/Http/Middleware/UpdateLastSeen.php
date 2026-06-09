<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    /** Interval minimum antar update ke database (menit). */
    private const THROTTLE_MINUTES = 5;

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $userId = $request->user()->id;
            $cacheKey = "user.last_seen.{$userId}";

            if (! Cache::has($cacheKey)) {
                User::whereKey($userId)->update(['last_seen' => now()]);
                Cache::put($cacheKey, true, now()->addMinutes(self::THROTTLE_MINUTES));
            }
        }

        return $next($request);
    }
}
