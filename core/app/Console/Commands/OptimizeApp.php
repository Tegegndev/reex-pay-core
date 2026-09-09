<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OptimizeApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize the application by clearing and caching configurations, views, etc.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Caching views...');
        Artisan::call('view:cache');
        $this->info('Optimizing application...');
        Artisan::call('optimize');
        $this->info('Application optimization complete.');
    }
}
