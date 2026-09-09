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
        Schema::create('navigations', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // For multilingual support
            $table->string('slug')->unique(); // Route slug or URL
            $table->foreignId('page_id')->nullable()->constrained('pages')->nullOnDelete(); // Optional page relation
            $table->unsignedInteger('order')->default(0); // Sorting
            $table->enum('target', ['_self', '_blank'])->default('_self'); // Link target
            $table->boolean('is_active')->default(true); // Enable/disable link
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigations');
    }
};
