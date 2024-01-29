<?php

namespace DonorServices\FilamentModule\Commands;

use Illuminate\Console\Command;

class FilamentModuleCommand extends Command
{
    public $signature = 'filament-module';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
