<?php

namespace App\Console\Commands\Development;

use NormanHuth\HelpersLaravel\App\Console\Commands\Development\PivotMigrateMakeCommand as Command;

class PivotMigrateMakeCommand extends Command
{
    protected function getFilename(): string
    {
        return getMigrationDatePrefix() . '_create_' . $this->table . '_pivot_table';
    }
}
