<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kyc_submissions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('kyc_template_id');
            $table->unsignedBigInteger('user_id');
            $table->json('submission_data')->nullable();
            $table->integer('status')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('kyc_template_id')
                ->references('id')
                ->on('kyc_templates')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_submissions');
    }
};
