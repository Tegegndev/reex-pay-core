<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IpInfoService
{
    /**
     * Retrieve IP information with country details.
     */
    public function getIpInfo(string $ip): ?array
    {
        // Retrieve API credentials from the plugin settings
        $credentials = pluginCredentials('ipinfo') ?? [];

        // If the plugin is disabled or access token is missing, use the fallback method
        if (! $credentials['status'] || empty($credentials['access_token'])) {
            return $this->fetchIpInfoFallback($ip);
        }

        return $this->fetchIpInfoFromApi($ip, $credentials['access_token']);
    }

    /**
     * Fetch IP information using the IpInfo API with caching.
     */
    private function fetchIpInfoFromApi(string $ip, string $apiToken): ?array
    {
        $cacheKey = "ipinfo:{$ip}";
        $cacheTtl = now()->addMinutes(60);

        return Cache::remember($cacheKey, $cacheTtl, function () use ($ip, $apiToken) {
            $url = "https://ipinfo.io/{$ip}?token={$apiToken}";

            try {
                $response = Http::timeout(5)->throw()->get($url);

                $data = $response->json();

                return $this->formatIpData($data);
            } catch (Exception $e) {
                Log::error('IpInfo API error', [
                    'ip'      => $ip,
                    'message' => $e->getMessage(),
                ]);

                return null;
            }
        });
    }

    /**
     * Fetch IP information using file_get_contents as a fallback.
     */
    private function fetchIpInfoFallback(string $ip): ?array
    {
        $url = "http://ipinfo.io/{$ip}/json";

        try {
            $locationData = @file_get_contents($url);

            if ($locationData === false) {
                throw new Exception("Fallback IP info API call failed for IP: {$ip}");
            }

            $data = json_decode($locationData, true);

            return $this->formatIpData($data);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    /**
     * Format IP data by adding country name and code.
     */
    private function formatIpData(array $data): array
    {
        $countryData = getCountryDataByCode($data['country'] ?? 'us');

        return array_merge($data, [
            'country_name' => $countryData['name']      ?? 'Unknown',
            'dial_code'    => $countryData['dial_code'] ?? 'Unknown',
        ]);
    }
}
