<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(10);

        return view('frontend.user.setting.notification', compact('notifications'));
    }

    public function recent()
    {
        $notifications = auth()->user()->getRecentNotifications();

        return view('frontend.layouts.user.partials._notifications', compact('notifications'))->render();
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();

            return redirect()->back();
        }

        return redirect()->back();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back();
    }
}
