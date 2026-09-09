<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payment_gateways') || DB::table('payment_gateways')->where('code', 'marzpay')->exists()) {
            return;
        }

        $gateway = [
            'logo'           => null,
            'name'           => 'REEXPAY Uganda Payments',
            'code'           => 'marzpay',
            'currencies'     => json_encode(['UGX']),
            'credentials'    => json_encode([
                'api_key'        => '',
                'api_secret'     => '',
                'webhook_secret' => '',
                'country'        => 'UG',
                'mode'           => 'sandbox',
            ]),
            'status'         => false,
            'created_at'     => now(),
            'updated_at'     => now(),
        ];

        $gateway[Schema::hasColumn('payment_gateways', 'is_withdraw') ? 'is_withdraw' : 'withdraw_field'] = 'phone_number';
        if (Schema::hasColumn('payment_gateways', 'ipn')) {
            $gateway['ipn'] = true;
        }

        DB::table('payment_gateways')->insert($gateway);
    }

    public function down(): void
    {
        DB::table('payment_gateways')->where('code', 'marzpay')->delete();
    }
};
