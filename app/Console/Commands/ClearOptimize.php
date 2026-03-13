<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearOptimize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:ops';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize, clear cache, and cache views and routes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Command::call('optimize:clear');
        Command::call('view:cache');
        Command::call('route:cache');
        Command::call('cache:clear');
        Command::call('config:cache');

        $this->info("Application is optimized, and views and routes are cached!");
        return 0;
    }
}
