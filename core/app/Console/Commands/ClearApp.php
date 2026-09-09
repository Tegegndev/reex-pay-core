<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear application cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing cache...');
        Artisan::call('cache:clear');

        $this->info('Clearing configuration cache...');
        Artisan::call('config:clear');

        $this->info('Clearing view cache...');
        Artisan::call('view:clear');

        $this->info('Clearing route cache...');
        Artisan::call('route:clear');

        $this->info('Clearing compiled views...');
        Artisan::call('optimize:clear');

        $this->info('Application optimization complete.');
    }
}
