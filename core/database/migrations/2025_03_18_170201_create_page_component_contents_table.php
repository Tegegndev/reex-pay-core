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
        Schema::create('page_component_repeated_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_id')->constrained('page_components')->onDelete('cascade');
            $table->json('content_data')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_component_contents');
    }
};
