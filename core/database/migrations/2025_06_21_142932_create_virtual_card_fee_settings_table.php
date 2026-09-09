<?php

// database/migrations/xxxx_xx_xx_create_virtual_card_fee_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual_card_fee_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
            $table->string('operation', 32);
            $table->string('fee_type', 32);
            $table->decimal('fee_amount', 12, 2);
            $table->decimal('min_amount', 12, 2)->default(0);
            $table->decimal('max_amount', 12, 2)->nullable();
            $table->integer('daily_txn_limit')->nullable();
            $table->decimal('daily_amount_limit', 16, 2)->nullable();
            $table->decimal('approval_threshold', 12, 2)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('provider_id')->references('id')->on('virtual_card_providers')->onDelete('cascade');
            $table->unique(['provider_id', 'currency_id', 'operation'], 'unique_fee_setting');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_card_fee_settings');
    }
};
