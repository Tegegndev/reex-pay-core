<?php

namespace App\Notifications\User;

use App\Enums\NotificationActionType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomMessageNotify extends Notification
{
    use Queueable;

    private string $message;   // Custom message content

    private array $via;        // Notification channels (e.g., email, SMS)

    private ?string $subject;  // Custom email subject

    private ?string $actionUrl; // Optional action URL

    private ?string $actionText; // Optional action button text

    /**
     * Create a new notification instance.
     */
    public function __construct(string $message, array $via)
    {
        $this->message    = $message;
        $this->via        = $via;
        $this->subject    = $subject    ?? 'Notification from Admin'; // Default subject if none provided
        $this->actionText = $actionText ?? 'View Notification'; // Default action text if none provided
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->via; // Return the channels specified when creating the notification
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('Notification from Admin') // Custom subject from admin
            ->line($this->message);    // Custom message from admin

        return $mailMessage->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'       => $this->subject ?? '',
            'message'     => $this->message,
            'icon'        => 'user-info-1',
            'action_type' => NotificationActionType::CREATED->value,
            'action_link' => $this->action ?? '',
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage(
            [
                'title'       => $this->subject ?? '',
                'message'     => $this->message,
                'icon'        => 'user-info-1',
                'action_type' => NotificationActionType::CREATED->value,
                'action_link' => $this->action ?? '',
                'timestamp'   => now()->toISOString(),
            ]
        );
    }
}
