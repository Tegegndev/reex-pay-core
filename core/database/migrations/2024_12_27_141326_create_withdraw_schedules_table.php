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
        Schema::create('withdraw_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('day', 10); // Day of the week (e.g., Sunday, Monday)
            $table->boolean('status')->default(false); // Enable (true) or Disable (false)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraw_schedules');
    }
};
