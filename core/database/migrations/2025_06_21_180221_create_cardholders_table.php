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
        Schema::create('cardholders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('gender', 16)->nullable();
            $table->date('dob')->nullable();
            $table->string('relation')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country', 10)->nullable();
            $table->string('card_type')->default('personal');
            $table->unsignedBigInteger('businesses_id')->nullable();
            $table->string('kyc_status')->default('pending');
            $table->string('kyc_type')->nullable(); // e.g. nid, passport, driving_license
            $table->string('address_proof_type')->nullable(); // e.g. utility_bill, bank_statement
            $table->json('kyc_documents')->nullable(); // All KYC docs as JSON mapping
            $table->string('note')->nullable();
            $table->string('status', 16)->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cardholders');
    }
};
