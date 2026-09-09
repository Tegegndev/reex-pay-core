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
        Schema::create('page_components', function (Blueprint $table) {
            $table->id();
            $table->string('component_icon')->nullable();
            $table->string('component_name');
            $table->string('component_key');
            $table->json('content_fields');
            $table->json('content_data');
            $table->string('type');
            $table->integer('sort');
            $table->boolean('repeated_content')->default(false);
            $table->boolean('is_modal')->default(false)->comment('Defines whether the component is managed via modal');
            $table->boolean('is_active')->default(true)->comment('Status of the component'); // Changing status to a boolean for better readability
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_components');
    }
};
