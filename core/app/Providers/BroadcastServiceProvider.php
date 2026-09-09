<?php

namespace App\Providers;

use App\Constants\Status;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Reyco Pay Solutions - Re-enable with error handling to prevent Laravel errors
        try {
            // Fetch Pusher configuration
            $pusherConfig = function_exists('pluginCredentials') ? pluginCredentials('pusher') : [];

            // Ensure Pusher is enabled before setting configuration
            if (! isset($pusherConfig['status']) || $pusherConfig['status'] !== Status::TRUE) {
                return;
            }

            // Validate required Pusher keys
            if (
                empty($pusherConfig['pusher_app_key']) || empty($pusherConfig['pusher_app_secret']) || empty($pusherConfig['pusher_app_id'])
            ) {
                return;
            }

            // Set Pusher configuration dynamically
            Config::set('broadcasting.default', 'pusher');

            Config::set('broadcasting.connections.pusher', [
                'driver'  => 'pusher',
                'key'     => $pusherConfig['pusher_app_key'],
                'secret'  => $pusherConfig['pusher_app_secret'],
                'app_id'  => $pusherConfig['pusher_app_id'],
                'options' => [
                    'cluster'   => $pusherConfig['pusher_app_cluster'] ?? 'mt1',
                    'useTLS'    => true,
                    'host'      => 'api-ap2.pusher.com',
                    'port'      => 443,
                    'scheme'    => 'https',
                    'encrypted' => true,
                ],
            ]);

            // Register broadcast routes
            Broadcast::routes(['middleware' => ['web']]);

            // Load broadcast channels file
            if (file_exists(base_path('routes/channels.php'))) {
                require base_path('routes/channels.php');
            }
        } catch (\Exception $e) {
            \Log::error('Broadcast provider configuration failed: ' . $e->getMessage());
        }
    }
}
