<?php

namespace Database\Seeders;

use App\Enums\NotificationActionType;
use App\Enums\UserType;
use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'identifier'  => 'virtual_card_admin_notify_request',
                'name'        => 'Virtual Card Request Submission',
                'icon'        => 'card-request',
                'action_type' => NotificationActionType::REQUESTED,
                'info'        => 'Admin is alerted when a user submits a new virtual card request.',
                'user_type'   => UserType::ADMIN,
                'variables'   => ['user', 'network', 'wallet'],
                'channels'    => [
                    [
                        'channel'   => 'email',
                        'title'     => 'New Virtual Card Request',
                        'message'   => 'User {user} submitted a virtual card request for {network} network (wallet: {wallet}). Please review and approve.',
                        'is_active' => true,
                    ],
                    [
                        'channel'   => 'push',
                        'title'     => 'Virtual Card Request',
                        'message'   => '{user} requested a {network} card.',
                        'is_active' => true,
                    ],
                    [
                        'channel'   => 'sms',
                        'title'     => null,
                        'message'   => 'Virtual card request: {user}, {network}, {wallet}',
                        'is_active' => false,
                    ],
                ],
            ],
            [
                'identifier'  => 'virtual_card_user_approved',
                'name'        => 'Virtual Card Approved',
                'icon'        => 'card-approved',
                'action_type' => NotificationActionType::APPROVED,
                'info'        => 'User is notified when their virtual card request is approved.',
                'user_type'   => UserType::USER,
                'variables'   => ['card_network', 'last4', 'wallet', 'fee'],
                'channels'    => [
                    [
                        'channel'   => 'email',
                        'title'     => 'Your Virtual Card is Ready!',
                        'message'   => 'Congratulations! Your {card_network} card (****{last4}) has been approved and added to your wallet ({wallet}). Issuing fee: {fee}.',
                        'is_active' => true,
                    ],
                    [
                        'channel'   => 'push',
                        'title'     => 'Virtual Card Approved',
                        'message'   => 'Your {card_network} card is approved.',
                        'is_active' => true,
                    ],
                    [
                        'channel'   => 'sms',
                        'title'     => null,
                        'message'   => '{card_network} card (****{last4}) approved.',
                        'is_active' => false,
                    ],
                ],
            ],
        ];

        foreach ($templates as $data) {
            // Find by identifier, update or create (to avoid duplicates)
            $template = NotificationTemplate::updateOrCreate(
                ['identifier' => $data['identifier']],
                collect($data)->except('channels')->toArray()
            );

            // Remove old channels and insert new
            $template->channels()->delete();
            $template->channels()->createMany($data['channels']);
        }
    }
}
