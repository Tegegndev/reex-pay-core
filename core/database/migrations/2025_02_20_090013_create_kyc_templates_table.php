<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kyc_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('title')->unique(); // e.g. "Passport Verification"
            $table->string('description')->nullable(); // e.g. "Upload your passport details"
            $table->json('fields'); // JSON array of field definitions
            $table->json('applicable_to'); // like ['user', 'merchant','both']
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_templates');
    }
};
