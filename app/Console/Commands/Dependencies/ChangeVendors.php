<?php

namespace App\Console\Commands\Dependencies;

use Illuminate\Console\Command;

class ChangeVendors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dependencies:change-vendors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change vendor dependencies files';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $file = base_path('vendor/laravel/framework/src/Illuminate/Database/Migrations/MigrationCreator.php');
        $contents = file_get_contents($file);
        $contents = str_replace('return date(\'Y_m_d_His\');', 'return getMigrationDatePrefix();', $contents);
        file_put_contents($file, $contents);

        $file = base_path('vendor/barryvdh/laravel-ide-helper/src/Console/ModelsCommand.php');
        if (file_exists($file)) {
            $contents = file_get_contents($file);
            $contents = str_replace(
                '} elseif (Str::startsWith($method, \'scope\') && $method !== \'scopeQuery\') {',
                '} elseif (Str::startsWith($method, \'scope\') && $method !== \'scopeQuery\' && $method !== \'scope\' && $method !== \'scopes\') {',
                $contents);
            file_put_contents($file, $contents);
        }
    }
}
