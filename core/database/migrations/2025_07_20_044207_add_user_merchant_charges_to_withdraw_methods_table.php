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
        Schema::table('withdraw_methods', function (Blueprint $table) {
            // Add user-specific charge fields
            $table->double('user_charge')->nullable()->after('charge')->comment('Charge amount for regular users');
            $table->string('user_charge_type')->default('percent')->after('user_charge')->comment('Charge type for regular users: fixed or percent (cast to FixPctType enum)');

            // Add merchant-specific charge fields
            $table->double('merchant_charge')->nullable()->after('user_charge_type')->comment('Charge amount for merchant users');
            $table->string('merchant_charge_type')->default('percent')->after('merchant_charge')->comment('Charge type for merchant users: fixed or percent (cast to FixPctType enum)');

            // Add index for better performance
            $table->index(['user_charge', 'merchant_charge'], 'idx_user_merchant_charges');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdraw_methods', function (Blueprint $table) {
            // Drop index first
            $table->dropIndex('idx_user_merchant_charges');

            // Drop the new columns
            $table->dropColumn([
                'user_charge',
                'user_charge_type',
                'merchant_charge',
                'merchant_charge_type',
            ]);
        });
    }
};
