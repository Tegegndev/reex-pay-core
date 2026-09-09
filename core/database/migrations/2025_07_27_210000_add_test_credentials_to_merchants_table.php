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
            // Test/Sandbox API credentials
            $table->string('test_api_key', 64)->nullable()->after('api_secret');
            $table->string('test_api_secret', 64)->nullable()->after('test_api_key');
            $table->string('test_merchant_key')->nullable()->after('test_api_secret');
            
            // Environment mode (production/sandbox)
            $table->enum('current_mode', ['production', 'sandbox'])->default('sandbox')->after('test_merchant_key');
            
            // Test mode settings
            $table->boolean('sandbox_enabled')->default(true)->after('current_mode');
            $table->text('test_webhook_urls')->nullable()->after('sandbox_enabled'); // JSON array of test webhook URLs
            
            // Add indexes for performance
            $table->index('test_api_key');
            $table->index('test_merchant_key');
            $table->index('current_mode');
            $table->index('sandbox_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropIndex(['test_api_key']);
            $table->dropIndex(['test_merchant_key']);
            $table->dropIndex(['current_mode']);
            $table->dropIndex(['sandbox_enabled']);
            
            $table->dropColumn([
                'test_api_key',
                'test_api_secret', 
                'test_merchant_key',
                'current_mode',
                'sandbox_enabled',
                'test_webhook_urls'
            ]);
        });
    }
};
