<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('currencies')->where('code', 'UGX')->update([
            'name' => 'Ugandan Shilling',
            'symbol' => 'UGX',
            'auto_wallet' => true,
            'default' => true,
            'status' => true,
            'updated_at' => now(),
        ]);
        DB::table('currencies')->where('code', '!=', 'UGX')->update([
            'auto_wallet' => false,
            'default' => false,
            'status' => false,
            'updated_at' => now(),
        ]);

        DB::table('wallets')->whereNotIn('currency_id', function ($query) {
            $query->select('id')->from('currencies')->where('code', 'UGX');
        })->update(['status' => false, 'updated_at' => now()]);

        DB::table('payment_gateways')->where('code', '!=', 'marzpay')->update([
            'status' => false,
            'updated_at' => now(),
        ]);

        $gateway = DB::table('payment_gateways')->where('code', 'marzpay')->first();
        $credentials = $gateway ? json_decode($gateway->credentials ?: '{}', true) : [];
        $marzPayConfigured = $gateway
            && ! empty($credentials['api_key'])
            && ! empty($credentials['api_secret'])
            && ! empty($credentials['webhook_secret']);

        if ($gateway) {
            DB::table('payment_gateways')->where('id', $gateway->id)->update([
                'name' => 'MarzPay Uganda Payments',
                'currencies' => json_encode(['UGX']),
                'status' => $marzPayConfigured,
                'updated_at' => now(),
            ]);
        }

        DB::table('deposit_methods')->where(function ($query) {
            $query->where('currency', '!=', 'UGX')
                ->orWhereNotIn('payment_gateway_id', $this->gatewayIds('marzpay'));
        })->update(['status' => false, 'updated_at' => now()]);
        DB::table('withdraw_methods')->where(function ($query) {
            $query->where('currency', '!=', 'UGX')
                ->orWhereNotIn('payment_gateway_id', $this->gatewayIds('marzpay'));
        })->update(['status' => false, 'updated_at' => now()]);

        if ($gateway && $marzPayConfigured) {
            DB::table('deposit_methods')->where($this->methodCode('deposit_methods'), 'marzpay-ugx')->update([
                'status' => true,
                'name' => 'MarzPay Mobile Money (UGX)',
                'updated_at' => now(),
            ]);
            DB::table('withdraw_methods')->where($this->methodCode('withdraw_methods'), 'marzpay-ugx')->update([
                'status' => true,
                'name' => 'MarzPay Mobile Money Withdrawal (UGX)',
                'updated_at' => now(),
            ]);
        }

        Cache::forget('payment_gateways_all');
        Cache::forget('all_currencies');
        Cache::forget('default_currency');
    }

    public function down(): void
    {
        // The previous currency and gateway state cannot be reconstructed safely.
    }

    private function gatewayIds(string $code): array
    {
        return DB::table('payment_gateways')->where('code', $code)->pluck('id')->all();
    }

    private function methodCode(string $table): string
    {
        return Schema::hasColumn($table, 'code') ? 'code' : 'method_code';
    }
};