<?php

namespace App\Console\Commands\Development;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install base app include data.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (config('app.env') !== 'local') {
            $this->warn('The `ide-helper` is only for local development environment');
            return;
        }

        $this->call('migrate:fresh');
        $this->call('scrap:twitch-scopes');
        $this->call('scrap:twitch-api-reference');
        $this->call('scrap:twitch-event-subs');
        $this->call('helper');
    }
}
