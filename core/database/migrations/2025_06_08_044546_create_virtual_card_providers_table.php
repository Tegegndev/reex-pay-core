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
        Schema::create('virtual_card_providers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_gateway_id')->nullable()->index();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('logo')->nullable();
            $table->string('brand')->nullable(); // e.g., Mastercard/Visa/Multi
            $table->string('description')->nullable();
            $table->json('supported_networks')->nullable();     // e.g., ["mastercard", "visa"]
            $table->json('supported_currencies')->nullable();   // e.g., ["USD", "EUR"]
            $table->decimal('issue_fee', 12, 2)->default(0.00); // always fixed, per card
            $table->decimal('min_balance', 12, 2)->nullable();  // optional: min wallet balance required
            $table->boolean('status')->default(true);
            $table->json('config')->nullable();
            $table->integer('order')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_card_providers');
    }
};
