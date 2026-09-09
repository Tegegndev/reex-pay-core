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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); // Primary key for the transactions table
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Reference to the user who performed the transaction
            $table->string('trx_id')->nullable(); // External transaction or reference ID (e.g., TXNTDUJLTASTGSZ)
            $table->string('trx_type'); // Type of transaction (e.g., deposit, withdrawal, payment)
            $table->text('description')->nullable(); // Optional human-readable description of the transaction
            $table->string('provider')->nullable(); // Transaction source or provider (e.g., stripe, paystack, user_wallet, admin_wallet)
            $table->string('processing_type'); // How the transaction was processed (e.g., auto, manual)
            $table->decimal('amount', 15, 2); // Total amount of the transaction
            $table->decimal('amount_flow')->nullable();
            $table->decimal('fee', 15, 2)->nullable(); // Transaction fee, if applicable
            $table->string('currency', 3)->default('USD'); // Transaction currency (ISO 3-character code)
            $table->decimal('net_amount', 15, 2)->default(0); // Net amount after fees and conversion
            $table->decimal('payable_amount', 15, 2)->nullable(); // The actual amount payable (may include conversions)
            $table->string('payable_currency', 3)->nullable(); // Currency for the payable amount (ISO 3-character code)
            $table->string('wallet_reference')->nullable(); // Wallet identifier for the transaction (if applicable)
            $table->string('trx_reference')->nullable(); // External reference ID (e.g., TXNTDUJLTASTGSZ)
            $table->json('trx_data')->nullable(); // Additional structured transaction data (e.g., bank account details, payment gateway metadata)
            $table->text('remarks')->nullable(); // Any remarks or messages for transaction approval/rejection
            $table->enum('status', ['pending', 'completed', 'failed']); // Current status of the transaction
            $table->timestamps(); // Timestamps for when the transaction was created and last updated
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
