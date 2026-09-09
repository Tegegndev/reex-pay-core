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
        Schema::create('footer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('footer_section_id')->constrained()->onDelete('cascade');

            $table->json('label')->nullable();      // {"en": "Contact Us", "es": "Contáctanos"}
            $table->text('content')->nullable();    // {"en": "...", "es": "..."} optional, for 'text' sections

            $table->enum('url_type', ['none', 'custom', 'page', 'social'])->default('none');
            $table->string('url')->nullable();
            $table->unsignedBigInteger('page_id')->nullable();
            $table->unsignedBigInteger('social_id')->nullable();

            $table->string('icon')->nullable(); // for social icons
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_items');
    }
};
