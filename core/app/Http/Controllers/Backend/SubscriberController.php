<?php

namespace App\Http\Controllers\Backend;

use App\Mail\SubscriberMail;
use App\Models\Subscriber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SubscriberController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'index'                     => 'subscriber-list',
            'sendMail|deleteSubscriber' => 'subscriber-manage',
        ];
    }

    public function index(Request $request): View
    {
        $query = Subscriber::query();

        // ✅ Search by email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('email', 'like', '%'.$search.'%');
        }

        // ✅ Filter by date range (format: "Mar 18, 2025 - Apr 16")
        if ($request->filled('daterange')) {
            $rawRange = $request->input('daterange');

            try {
                [$startDate, $endDate] = explode(',', $rawRange);

                $query->whereBetween('subscribed_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ]);
            } catch (\Throwable $e) {
                logger()->warning('Invalid date range format for filtering.', [
                    'daterange' => $rawRange,
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        $subscribers = $query->latest()->paginate(20)->withQueryString();

        return view('backend.subscriber.index', compact('subscribers'));
    }

    public function sendMail(Request $request)
    {
        $request->validate([
            'subject'       => 'required|string|max:255',
            'message'       => 'required|string',
            'subscriber_id' => 'nullable|exists:subscribers,id', // only for single send
        ]);

        $subject = $request->subject;
        $message = $request->message;

        if ($request->filled('subscriber_id')) {
            // ✅ Single Mail
            $subscriber = Subscriber::find($request->subscriber_id);
            Mail::to($subscriber->email)->send(new SubscriberMail($subject, $message));
        } else {
            // ✅ Bulk Mail to all subscribers
            Subscriber::chunk(50, function ($subscribers) use ($subject, $message) {
                foreach ($subscribers as $subscriber) {
                    Mail::to($subscriber->email)->queue(new SubscriberMail($subject, $message));
                }
            });

        }
        notifyEvs('success', 'Mail sent successfully.');

        return back();
    }

    public function deleteSubscriber($id)
    {
        $subscriber = Subscriber::find($id);

        if (! $subscriber) {
            notifyEvs('error', __('Subscriber not found'));

            return to_route('admin.subscriber.index');
        }

        $subscriber->delete();
        notifyEvs('success', __('Subscriber deleted successfully'));

        return to_route('admin.subscriber.index');
    }
}
