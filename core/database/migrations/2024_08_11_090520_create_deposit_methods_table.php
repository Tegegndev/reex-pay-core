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
        Schema::create('deposit_methods', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_gateway_id')->comment('Payment gateway id');
            $table->string('icon');
            $table->string('name');
            $table->enum('type', ['auto', 'manual'])->comment('auto = automatic, manual = manual');
            $table->string('code');
            $table->string('currency');
            $table->string('currency_symbol');
            $table->double('min_limit');
            $table->double('max_limit');
            $table->enum('rate_type', ['fixed', 'live'])->comment('fixed = fixed rate, live = live rate');
            $table->double('rate');
            $table->enum('charge_type', ['fixed', 'percent'])->comment('fixed = fixed charge, percent = percent charge');
            $table->double('charge');
            $table->longText('fields');
            $table->longText('notes');
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_methods');
    }
};
