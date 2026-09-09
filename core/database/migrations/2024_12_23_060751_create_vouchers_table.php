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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('code')->unique();
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('currency_id')->nullable(); // Tie voucher to specific currency (optional)
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('redeemed_by')->nullable(); // User who redeemed
            $table->unsignedBigInteger('redeemed_wallet_id')->nullable(); // Wallet used for redemption
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
