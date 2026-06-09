<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class UserCache
{
    public static function forgetNotifications(int $userId): void
    {
        Cache::forget("layout.notifications.{$userId}");
    }

    public static function forgetDashboard(int $userId): void
    {
        Cache::forget('dashboard.stats.' . $userId . '.' . now()->format('Y-m-d'));
    }

    public static function forgetLastSeen(int $userId): void
    {
        Cache::forget("user.last_seen.{$userId}");
    }
}
