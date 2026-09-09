<?php

namespace App\Listeners;

use App\Models\LoginActivity;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Jenssegers\Agent\Agent;

class LogSuccessfulLogin
{
    public function __construct()
    {
        //
    }

    public function handle(Login $event)
    {
        if (! ($event->user instanceof User)) {
            return;
        }

        $request = request();
        $agent   = new Agent;
        $agent->setUserAgent($request->userAgent());

        $location = \IpInfo::getIpInfo($request->ip());

        LoginActivity::create([
            'user_id'    => $event->user->id,
            'ip_address' => $request->ip(),
            'country'    => $location['country_name']
                    ?? $event->user->country
                    ?? 'US',
            'device'     => $agent->device() ?: 'Unknown',
            'platform'   => $agent->platform() ?: 'Unknown',
            'browser'    => $agent->browser() ?: 'Unknown',
            'user_agent' => $request->userAgent(),
            'login_at'   => now(),
        ]);

    }
}
