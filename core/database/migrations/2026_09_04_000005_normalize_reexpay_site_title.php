<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->where('key', 'site_title')->update([
            'val' => 'REEXPAY LIMITED',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'site_title')->update([
            'val' => 'Coevs',
            'updated_at' => now(),
        ]);
    }
};