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
        Schema::create('withdraw_methods', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->integer('payment_gateway_id')->comment('Payment gateway id');
            $table->string('icon')->nullable()->comment('Icon for the withdraw method');
            $table->string('name')->comment('Name of the withdraw method');
            $table->enum('type', ['auto', 'manual'])->comment('auto = automatic, manual = manual');
            $table->string('code')->comment('Unique code for the withdraw method');
            $table->string('currency')->comment('Currency of the withdrawal');
            $table->string('currency_symbol')->comment('Currency symbol');
            $table->double('min_limit')->comment('Minimum withdrawal limit');
            $table->double('max_limit')->comment('Maximum withdrawal limit');
            $table->enum('rate_type', ['fixed', 'live'])->comment('fixed = fixed rate, live = live rate');
            $table->double('rate')->comment('Exchange rate');
            $table->enum('charge_type', ['fixed', 'percent'])->comment('fixed = fixed charge, percent = percent charge');
            $table->double('charge')->comment('Fee charged for withdrawals');
            $table->integer('process_time_value')->default(0)->comment('Processing time value');
            $table->enum('process_time_unit', ['minute', 'hour', 'day'])->default('minute')->comment('Processing time unit');
            $table->longText('fields')->nullable()->comment('Additional fields required for the withdrawal method');
            $table->tinyInteger('status')->default(1)->comment('1 = active, 0 = inactive');
            $table->timestamps(); // Created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraw_methods');
    }
};
