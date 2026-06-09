<?php

namespace App\Http\Controllers;

use App\Support\UserCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        UserCache::forgetNotifications(Auth::id());

        return redirect()->back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            UserCache::forgetNotifications(Auth::id());

            return redirect($notification->data['link'] ?? route('dashboard'));
        }

        return redirect()->back();
    }
}
