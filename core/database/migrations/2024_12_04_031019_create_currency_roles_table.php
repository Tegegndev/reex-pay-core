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
        Schema::create('currency_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->string('role_name');
            $table->double('min_limit')->nullable();
            $table->double('max_limit')->nullable();
            $table->enum('fee_type', ['fixed', 'percent'])->comment('fixed = fixed fee, percent = percentage fee')->nullable();
            $table->double('fee')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_roles');
    }
};
