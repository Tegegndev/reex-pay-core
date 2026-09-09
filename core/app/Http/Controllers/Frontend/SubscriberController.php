<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email:dns|unique:subscribers,email',
        ]);

        Subscriber::create([
            'email'         => $validated['email'],
            'ip_address'    => $request->ip(),
            'subscribed_at' => now(),
        ]);
        notifyEvs('success', 'Subscribed Successfully!');

        return redirect()->back();
    }
}
