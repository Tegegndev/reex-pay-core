<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'index|recent'                 => 'notification-list',
            'notifyUsers|sendNotification' => 'custom-notify-users',
        ];
    }

    /**
     * Display paginated notifications.
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(10);

        return view('backend.notifications.index', compact('notifications'));
    }

    /**
     * Return recent notifications for the dropdown.
     */
    public function recent()
    {
        $notifications = auth()->user()->getRecentNotifications();

        return view('backend.layouts.partials._notifications', compact('notifications'))->render();
    }

    public function notifyUsers()
    {
        return view('backend.notifications.notify_users');
    }

    public function sendNotification(Request $request)
    {
        $validated = $request->validate([
            'user_id'     => ['nullable'],
            'user_type'   => $request->filled('user_id') ? ['nullable'] : ['required'],
            'notify_type' => 'required|in:email,push',
            'title'       => $request->input('notify_type') === 'push'
                ? 'nullable|string|max:255'
                : 'required|string|max:255',
            'message'     => 'required|string',
            'schedule_at' => 'nullable|date|after_or_equal:now',
        ]);

        // Handle 'all' or convert string to array only for bulk user
        $userTypes = [];
        if (! $request->filled('user_id')) {
            $userTypes = $validated['user_type'] === 'all'
                ? \App\Enums\UserRole::all()
                : (array) $validated['user_type'];

            $userTypes = collect($userTypes)
                ->map(fn ($value) => \App\Enums\UserRole::tryFrom($value))
                ->filter()
                ->pluck('value')
                ->all();
        }

        // Apply timezone config and convert schedule time
        $timezone = setting('site_timezone', config('app.timezone', 'UTC'));

        $scheduleAt = isset($validated['schedule_at'])
            ? \Carbon\Carbon::parse($validated['schedule_at'], $timezone)->timezone($timezone)
            : now($timezone);

        $data = [
            'user_id'     => $validated['user_id'] ?? null,
            'user_types'  => $userTypes,
            'notify_type' => $validated['notify_type'],
            'title'       => $validated['title'] ?? null,
            'message'     => $validated['message'],
            'schedule_at' => $scheduleAt,
        ];

        dispatch(new \App\Jobs\NotifyUsers($data))->delay($scheduleAt);

        notifyEvs('success', 'Notification sent successfully.');

        return back();
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead()
    {
        // Use each() to iterate over the collection and mark each as read.
        auth()->user()->unreadNotifications->each->markAsRead();

        return redirect()->back();
    }

    /**
     * Mark a single notification as read.
     *
     * @param string $id
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back();
    }
}
