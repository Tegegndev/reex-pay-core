<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deposit_methods', function (Blueprint $table) {
            // Add user charge fields
            $table->double('user_charge')->nullable()->after('charge');
            $table->string('user_charge_type')->default('percent')->after('user_charge');

            // Add merchant charge fields
            $table->double('merchant_charge')->nullable()->after('user_charge_type');
            $table->string('merchant_charge_type')->default('percent')->after('merchant_charge');

            // Add indexes for performance
            $table->index('user_charge');
            $table->index('merchant_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposit_methods', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['user_charge']);
            $table->dropIndex(['merchant_charge']);

            // Drop columns
            $table->dropColumn([
                'user_charge',
                'user_charge_type',
                'merchant_charge',
                'merchant_charge_type',
            ]);
        });
    }
};
