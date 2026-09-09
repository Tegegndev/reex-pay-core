<?php

namespace Database\Seeders;

use App\Constants\RewardType;
use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (RewardType::TYPES as $key => $type) {
            Reward::insert([
                ['type' => $key, 'level' => 1, 'percentage' => 10.00, 'created_at' => now(), 'updated_at' => now()],
                ['type' => $key, 'level' => 2, 'percentage' => 5.00, 'created_at' => now(), 'updated_at' => now()],
                ['type' => $key, 'level' => 3, 'percentage' => 2.00, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
