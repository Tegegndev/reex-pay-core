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
        Schema::table('virtual_card_requests', function (Blueprint $table) {
            $table->foreignId('cardholder_id')->nullable()->after('user_id')->constrained('cardholders')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('virtual_card_requests', function (Blueprint $table) {
            $table->dropForeign(['cardholder_id']);
            $table->dropColumn('cardholder_id');
        });
    }
};
