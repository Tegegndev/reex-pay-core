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
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('merchant_key')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('business_name')->index(); // Indexed for better search performance
            $table->string('site_url')->unique();
            $table->unsignedBigInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
            $table->string('business_logo')->nullable(); // Consider whether logos are mandatory
            $table->string('business_email')->nullable();
            $table->text('business_description')->nullable();
            $table->double('fee')->default(0);
            $table->string('api_key', 64)->nullable(); // Assuming an API key length of 64 characters
            $table->string('api_secret', 64)->nullable(); // Assuming an API secret length of 64 characters
            $table->string('status')->default('pending')->index(); // Default status
            $table->timestamps();
            $table->softDeletes(); // Enables soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
