<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserFeature;
use Illuminate\Console\Command;

class SyncUserFeatures extends Command
{
    protected $signature = 'feature:sync';

    protected $description = 'Sync user features from config file to database for all users';

    public function handle()
    {
        // Retrieve all users
        $users = User::all();

        if ($users->isEmpty()) {
            $this->warn('No users found. Skipping feature sync.');

            return;
        }

        // Loop through each user and sync features
        foreach ($users as $user) {
            UserFeature::syncWithConfigForUser($user->id);
        }

        $this->info('User features synced successfully for all users.');
    }
}
