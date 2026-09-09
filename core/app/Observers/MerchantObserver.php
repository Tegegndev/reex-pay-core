<?php

namespace App\Observers;

use App\Enums\EnvironmentMode;
use App\Enums\MerchantStatus;
use App\Models\Merchant;
use Illuminate\Support\Str;

class MerchantObserver
{
    public function creating(Merchant $merchant): void
    {
        // Always set production API keys for new merchants
        $merchant->api_key      = Str::random(28);
        $merchant->api_secret   = Str::random(38);
        $merchant->merchant_key = Str::random(12);
        $merchant->status       = MerchantStatus::PENDING;
        
        // Generate test/sandbox credentials automatically
        $merchant->test_api_key      = 'test_' . Str::random(40);
        $merchant->test_api_secret   = 'test_secret_' . Str::random(32);
        $merchant->test_merchant_key = 'test_merchant_' . Str::random(16);
        
        // Set default sandbox settings using enum
        $merchant->current_mode     = EnvironmentMode::SANDBOX; // Start in sandbox mode for safety
        $merchant->sandbox_enabled  = true;                     // Enable sandbox by default
    }

    public function created(Merchant $merchant): void
    {
        // Log merchant creation with environment details
        \Illuminate\Support\Facades\Log::info('Merchant created with sandbox/production credentials', [
            'merchant_id' => $merchant->id,
            'business_name' => $merchant->business_name,
            'sandbox_enabled' => $merchant->sandbox_enabled,
            'current_mode' => $merchant->current_mode->value
        ]);
    }
}
