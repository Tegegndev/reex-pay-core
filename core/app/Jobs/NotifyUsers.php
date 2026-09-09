<?php

namespace App\Jobs;

use App\Enums\UserStatus;
use App\Mail\GenericNotification;
use App\Models\User;
use App\Notifications\User\CustomMessageNotify;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class NotifyUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userQuery = User::query()
            ->where('status', UserStatus::ACTIVE);

        if (! empty($this->data['user_id'])) {
            $userQuery->where('id', $this->data['user_id']);
        } else {
            $userQuery->whereIn('role', $this->data['user_types']);
        }

        $users = $userQuery->get();

        if ($this->data['notify_type'] === 'email') {
            foreach ($users as $user) {
                Mail::to($user->email)->queue(
                    new GenericNotification(
                        $this->data['title'] ?? '',
                        $this->data['message']
                    )
                );
            }
        }

        if ($this->data['notify_type'] === 'push') {
            $pusherStatus = pluginCredentials('pusher')['status'] ?? false;
            $via          = $pusherStatus ? ['database', 'broadcast'] : ['database'];
            Notification::send(
                $users,
                new CustomMessageNotify(
                    $this->data['message'],
                    $via
                )
            );
        }
    }
}
