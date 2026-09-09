<?php

namespace Database\Seeders;

use App\Models\VirtualCardProvider;
use Illuminate\Database\Seeder;

class VirtualCardProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VirtualCardProvider::updateOrCreate(
            ['code' => 'stripe'],
            [
                'name'                 => 'Stripe Issuing',
                'logo'                 => 'general/static/gateway/stripe.png',
                'brand'                => 'Multi',
                'supported_networks'   => ['mastercard', 'visa'],
                'supported_currencies' => ['USD', 'EUR', 'GBP'],
                'issue_fee'            => 2.00,
                'min_balance'          => 10.00,
                'status'               => true,
                'order'                => 1,
            ]
        );

        VirtualCardProvider::updateOrCreate(
            ['code' => 'strowallet'],
            [
                'name'                 => 'StroWallet Provider',
                'logo'                 => 'general/static/gateway/strowallet.png',
                'brand'                => 'Multi',
                'supported_networks'   => ['mastercard', 'visa'],
                'supported_currencies' => ['USD', 'NGN'],
                'issue_fee'            => 1.50,
                'min_balance'          => 5.00,
                'status'               => true,
                'order'                => 2,
            ]
        );
    }
}
