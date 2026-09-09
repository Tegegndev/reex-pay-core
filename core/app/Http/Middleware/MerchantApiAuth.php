<?php

namespace App\Http\Middleware;

use App\Enums\EnvironmentMode;
use App\Models\Merchant;
use Closure;
use Illuminate\Http\Request;

class MerchantApiAuth
{
    /**
     * Handle an incoming request.
     *
     * Expected headers:
     * - X-Merchant-Key
     * - X-API-Key
     * - X-Signature (HMAC signature of the JSON payload using the merchant's API secret)
     * - X-Environment (optional: 'production' or 'sandbox', defaults to 'production')
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey      = $request->header('X-API-Key');
        $merchantKey = $request->header('X-Merchant-Key');
        $environment = $request->header('X-Environment', EnvironmentMode::PRODUCTION->value);


        // Validate environment header using enum
        $validEnvironments = array_column(EnvironmentMode::cases(), 'value');
        if (!in_array($environment, $validEnvironments)) {
            return response()->json([
                'error' => 'Invalid environment',
                'message' => 'Use "production" or "sandbox"',
                'valid_environments' => $validEnvironments
            ], 400);
        }

        $environmentEnum = EnvironmentMode::from($environment);
        $merchant = $this->findMerchantByCredentials($apiKey, $merchantKey, $environmentEnum);

        if (!$merchant) {
            return response()->json([
                'error' => 'Unauthorized', 
                'message' => 'Invalid API credentials for the specified environment'
            ], 401);
        }

        // For sandbox mode, ensure sandbox is enabled
        if ($environmentEnum->isSandbox() && !$merchant->sandbox_enabled) {
            return response()->json([
                'error' => 'Sandbox Disabled',
                'message' => 'Sandbox mode is disabled for this merchant'
            ], 403);
        }

        // Set the current environment on the merchant model
        $merchant->current_environment = $environmentEnum;

        $request->merge([
            'merchant' => $merchant,
            'environment' => $environmentEnum->value,
            'is_sandbox' => $environmentEnum->isSandbox()
        ]);

        return $next($request);
    }

    /**
     * Find merchant by credentials based on environment
     */
    private function findMerchantByCredentials(string $apiKey, string $merchantKey, EnvironmentMode $environment): ?Merchant
    {
        if ($environment->isSandbox()) {
            // Search by test credentials
            return Merchant::where('test_api_key', $apiKey)
                ->where('test_merchant_key', $merchantKey)
                ->where('sandbox_enabled', true)
                ->first();
        } else {
            // Search by production credentials
            return Merchant::where('api_key', $apiKey)
                ->where('merchant_key', $merchantKey)
                ->first();
        }
    }
}
