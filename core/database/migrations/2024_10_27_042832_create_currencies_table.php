<?php

use App\Constants\CurrencyType;
use App\Constants\Status;
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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('flag')->nullable();
            $table->string('name');
            $table->string('code');
            $table->string('symbol');
            $table->enum('type', CurrencyType::getTypes());
            $table->double('exchange_rate')->default(1);
            $table->boolean('rate_live')->default(false);
            $table->boolean('auto_wallet')->default(false);
            $table->boolean('default')->default(false);
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
