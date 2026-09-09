<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReycoCurrencySeeder extends Seeder
{
    /**
     * Seed the Reyco Pay Solutions default currency (UGX).
     */
    public function run(): void
    {
        try {
            // Create or update UGX (Ugandan Shilling) as the default currency
            DB::table('currencies')->updateOrInsert(
                ['code' => 'UGX'],
                [
                    'name' => 'Ugandan Shilling',
                    'symbol' => 'UGX',
                    'flag' => '🇺🇬',
                    'type' => 'fiat',
                    'exchange_rate' => 1.0,
                    'rate_live' => 0,
                    'auto_wallet' => 1,
                    'default' => 1,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Ensure no other currency is set as default
            DB::table('currencies')->where('code', '!=', 'UGX')->update(['default' => 0]);
        } catch (\Exception $e) {
            // Log error but don't fail the seeder
            \Log::error('Failed to create UGX currency: ' . $e->getMessage());
        }
    }
}