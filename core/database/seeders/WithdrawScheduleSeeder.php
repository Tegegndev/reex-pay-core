<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class WithdrawScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        foreach ($days as $day) {
            DB::table('withdraw_schedules')->insert([
                'day'        => $day,
                'status'     => false, // Default to "Disabled"
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
