<?php

namespace App\Console\Commands;

use App\Helpers\BusinessConfig;
use Illuminate\Console\Command;

class ClearBusinessConfigCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:clear-business';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear cached business configuration data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        BusinessConfig::clearCache();
        $this->info('Business configuration cache cleared successfully!');
    }
}
