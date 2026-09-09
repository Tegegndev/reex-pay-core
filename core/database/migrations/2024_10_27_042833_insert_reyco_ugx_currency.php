<?php

use App\Constants\CurrencyType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reyco Pay Solutions - Insert UGX as default currency
        DB::table('currencies')->insert([
            'flag' => '🇺🇬',
            'name' => 'Ugandan Shilling',
            'code' => 'UGX',
            'symbol' => 'UGX',
            'type' => 'fiat',
            'exchange_rate' => 1.0,
            'rate_live' => false,
            'auto_wallet' => true,
            'default' => true,
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('currencies')->where('code', 'UGX')->delete();
    }
};