<?php

namespace App\Notifications\Admin;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotify extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    // This method determines which channels the notification will be sent to
    public function via($notifiable)
    {
        // In this case, we're sending the notification via email
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = url(route('admin.reset.password.now', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Admin Password Reset Request')
            ->line('You are receiving this email because we received a password reset request for your admin account.')
            ->action('Reset Password', $resetUrl)
            ->line('If you did not request a password reset, no further action is required.');
    }
}
