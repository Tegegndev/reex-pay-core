<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Handle Contact Form Submission.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['required', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        try {
            Mail::send('emails.contact', [
                'name'    => $validated['name'],
                'email'   => $validated['email'],
                'phone'   => $validated['phone'],
                'subject' => $validated['subject'],
                'body'    => $validated['message'],
            ], function ($message) use ($validated) {
                $message->to(setting('support_email', config('mail.from.address')))
                    ->from(config('mail.from.address'), config('app.name')) // Always your own email
                    ->replyTo($validated['email'], $validated['name'])      // User's email for reply
                    ->subject(__('New Contact Message: :subject', ['subject' => $validated['subject']]));
            });

            notifyEvs('success', __('Thank you! Your message has been sent successfully.'));

            return redirect()->back();

        } catch (\Exception $e) {
            notifyEvs('error', __('Something went wrong. Please try again later.'));

            return redirect()->back();
        }
    }
}
