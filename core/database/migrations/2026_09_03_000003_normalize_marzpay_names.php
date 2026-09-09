<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('payment_gateways')
            ->where('code', 'marzpay')
            ->update(['name' => 'MarzPay Uganda Payments', 'updated_at' => now()]);

        DB::table('deposit_methods')
            ->where($this->methodCode('deposit_methods'), 'marzpay-ugx')
            ->update(['name' => 'MarzPay Mobile Money (UGX)', 'updated_at' => now()]);

        DB::table('withdraw_methods')
            ->where($this->methodCode('withdraw_methods'), 'marzpay-ugx')
            ->update(['name' => 'MarzPay Mobile Money Withdrawal (UGX)', 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('payment_gateways')
            ->where('code', 'marzpay')
            ->update(['name' => 'REEXPAY Uganda Payments', 'updated_at' => now()]);

        DB::table('deposit_methods')
            ->where($this->methodCode('deposit_methods'), 'marzpay-ugx')
            ->update(['name' => 'REEXPAY Mobile Money (UGX)', 'updated_at' => now()]);

        DB::table('withdraw_methods')
            ->where($this->methodCode('withdraw_methods'), 'marzpay-ugx')
            ->update(['name' => 'REEXPAY Mobile Money Withdrawal (UGX)', 'updated_at' => now()]);
    }

    private function methodCode(string $table): string
    {
        return Schema::hasColumn($table, 'code') ? 'code' : 'method_code';
    }
};