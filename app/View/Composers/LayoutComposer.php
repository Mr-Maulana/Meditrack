<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class LayoutComposer
{
    public function compose(View $view): void
    {
        if (! Auth::check()) {
            return;
        }

        $userId = Auth::id();

        $data = Cache::remember("layout.notifications.{$userId}", 60, function () {
            $user = Auth::user();

            return [
                'unreadCount' => $user->unreadNotifications()->count(),
                'notifications' => $user->notifications()->latest()->take(5)->get(),
            ];
        });

        $view->with($data);
    }
}
