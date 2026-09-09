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
        Schema::create('virtual_card_requests', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('virtual_card_providers')->cascadeOnDelete();
            $table->string('network');
            $table->string('status')->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamp('admin_reviewed_at')->nullable();
            $table->timestamp('provider_issued_at')->nullable();
            $table->json('provider_response')->nullable(); // response/audit info
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_card_requests');
    }
};
