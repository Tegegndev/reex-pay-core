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
        Schema::table('merchants', function (Blueprint $table) {
            // Drop the test_webhook_urls field since we'll use single webhook_url
            $table->dropColumn('test_webhook_urls');
            
            // Add single webhook_url field for all environments
            $table->string('webhook_url')->nullable()->after('sandbox_enabled');
            
            // Update current_mode to use enum values
            $table->dropColumn('current_mode');
            $table->enum('current_mode', ['sandbox', 'production'])->default('sandbox')->after('webhook_url');
            
            // Add index for webhook_url for performance
            $table->index('webhook_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            // Restore test_webhook_urls field
            $table->text('test_webhook_urls')->nullable()->after('sandbox_enabled');
            
            // Remove webhook_url field
            $table->dropIndex(['webhook_url']);
            $table->dropColumn('webhook_url');
            
            // Revert current_mode to string
            $table->dropColumn('current_mode');
            $table->enum('current_mode', ['production', 'sandbox'])->default('sandbox');
        });
    }
};
