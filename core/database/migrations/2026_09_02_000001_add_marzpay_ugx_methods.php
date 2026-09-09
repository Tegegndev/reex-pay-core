<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $gateway = DB::table('payment_gateways')->where('code', 'marzpay')->first();

        if (! $gateway) {
            return;
        }

        $now = now();
        $charge = 6.0;
        $common = [
            'payment_gateway_id'   => $gateway->id,
            'currency'             => 'UGX',
            'currency_symbol'      => 'UGX',
            'charge_type'          => 'percent',
            'charge'               => $charge,
            'user_charge'          => $charge,
            'user_charge_type'     => 'percent',
            'merchant_charge'      => $charge,
            'merchant_charge_type' => 'percent',
            'status'               => false,
            'created_at'           => $now,
            'updated_at'           => $now,
        ];

        $depositCode = Schema::hasColumn('deposit_methods', 'code') ? 'code' : 'method_code';
        $depositMin = Schema::hasColumn('deposit_methods', 'min_limit') ? 'min_limit' : 'min_deposit';
        $depositMax = Schema::hasColumn('deposit_methods', 'max_limit') ? 'max_limit' : 'max_deposit';
        $common[Schema::hasColumn('deposit_methods', 'icon') ? 'icon' : 'logo'] = '';
        if (Schema::hasColumn('deposit_methods', 'rate_type')) {
            $common['rate_type'] = 'fixed';
            $common['rate'] = 1;
        } else {
            $common['conversion_rate_live'] = false;
            $common['conversion_rate'] = 1;
        }

        if (! DB::table('deposit_methods')->where($depositCode, 'marzpay-ugx')->exists()) {
            DB::table('deposit_methods')->insert(array_merge($common, [
                'name'                  => 'REEXPAY Mobile Money (UGX)',
                'type'                  => 'auto',
                $depositCode            => 'marzpay-ugx',
                $depositMin             => 500,
                $depositMax             => 10000000,
                'fields'                => json_encode([]),
                (Schema::hasColumn('deposit_methods', 'notes') ? 'notes' : 'receive_payment_details') => '',
            ]));
        }

        $withdrawCode = Schema::hasColumn('withdraw_methods', 'code') ? 'code' : 'method_code';
        $withdrawMin = Schema::hasColumn('withdraw_methods', 'min_limit') ? 'min_limit' : 'min_withdraw';
        $withdrawMax = Schema::hasColumn('withdraw_methods', 'max_limit') ? 'max_limit' : 'max_withdraw';
        $withdrawCommon = $common;
        if (Schema::hasColumn('withdraw_methods', 'rate_type')) {
            $withdrawCommon['rate_type'] = 'fixed';
            $withdrawCommon['rate'] = 1;
        } else {
            unset($withdrawCommon['rate_type'], $withdrawCommon['rate']);
            $withdrawCommon['conversion_rate_live'] = false;
            $withdrawCommon['conversion_rate'] = 1;
        }
        if (Schema::hasColumn('withdraw_methods', 'icon')) {
            $withdrawCommon['icon'] = '';
        } else {
            $withdrawCommon['logo'] = null;
            unset($withdrawCommon['icon']);
        }

        if (! DB::table('withdraw_methods')->where($withdrawCode, 'marzpay-ugx')->exists()) {
            DB::table('withdraw_methods')->insert(array_merge($withdrawCommon, [
                'name'             => 'REEXPAY Mobile Money Withdrawal (UGX)',
                'type'             => 'auto',
                $withdrawCode     => 'marzpay-ugx',
                $withdrawMin      => 1000,
                $withdrawMax      => 5000000,
                'process_time_value' => 0,
                'process_time_unit' => 'minute',
                'fields'           => json_encode([[
                    'name'       => 'phone_number',
                    'type'       => 'text',
                    'validation' => 'required',
                ]]),
            ]));
        }
    }

    public function down(): void
    {
        DB::table('deposit_methods')->where(Schema::hasColumn('deposit_methods', 'code') ? 'code' : 'method_code', 'marzpay-ugx')->delete();
        DB::table('withdraw_methods')->where(Schema::hasColumn('withdraw_methods', 'code') ? 'code' : 'method_code', 'marzpay-ugx')->delete();
    }
};
