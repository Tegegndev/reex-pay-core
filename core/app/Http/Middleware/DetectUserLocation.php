<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Fluent;

class DetectUserLocation
{
    public function handle(Request $request, Closure $next)
    {
        if (! Session::has('user_location')) {
            $location = $this->getLocation($request->ip());
            Session::put('user_location', $location);
        }

        return $next($request);
    }

    private function getLocation($clientIp)
    {
        $ip = $clientIp === '127.0.0.1' ? '103.77.188.202' : $clientIp;
        try {
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}");

            if ($response->successful()) {
                $locationData = $response->json();
                // Fetch country details from the cached JSON data
                $currentCountry = getCountryByCode($locationData['countryCode'] ?? null);

                return new Fluent([
                    'country_code' => $currentCountry['code']      ?? null,
                    'name'         => $currentCountry['name']      ?? null,
                    'dial_code'    => $currentCountry['dial_code'] ?? null,
                    'ip'           => $locationData['query']       ?? $ip,
                ]);
            } else {
                Log::warning('IP API failed', ['ip' => $ip, 'status' => $response->status()]);
            }
        } catch (\Exception $e) {
            Log::error('IP API error', ['ip' => $ip, 'error' => $e->getMessage()]);
        }

        return new Fluent([
            'country_code' => null,
            'name'         => 'Unknown',
            'dial_code'    => null,
            'ip'           => $ip,
        ]);
    }
}
