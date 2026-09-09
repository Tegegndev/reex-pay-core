<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Reyco Pay Solutions - Seed UGX (Ugandan Shilling) as default currency
        try {
            $this->call(\Database\Seeders\ReycoCurrencySeeder::class);
        } catch (\Exception $e) {
            // If seeder fails, create currency directly
            $this->createDefaultCurrency();
        }

        // $this->call(PermissionTableSeeder::class);
        // $this->call(WithdrawScheduleSeeder::class);
        // $this->call(VirtualCardProviderSeeder::class);
        // $this->call(PaymentGatewaySeeder::class);
        // $this->call(NotificationTemplateSeeder::class);
    }

    /**
     * Create default currency directly if seeder fails.
     * Reyco Pay Solutions fallback to prevent Laravel errors.
     */
    protected function createDefaultCurrency(): void
    {
        try {
            Currency::updateOrCreate(
                ['code' => 'UGX'],
                [
                    'name' => 'Ugandan Shilling',
                    'symbol' => 'UGX',
                    'flag' => '🇺🇬',
                    'type' => 'fiat',
                    'exchange_rate' => 1.0,
                    'rate_live' => false,
                    'auto_wallet' => true,
                    'default' => true,
                    'status' => true,
                ]
            );
        } catch (\Exception $e) {
            // Log error but don't fail the seeder
            \Log::error('Failed to create default currency: ' . $e->getMessage());
        }
    }
}
