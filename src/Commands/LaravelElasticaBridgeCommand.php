<?php

namespace Limenet\LaravelElasticaBridge\Commands;

use Illuminate\Console\Command;

class LaravelElasticaBridgeCommand extends Command
{
    public $signature = 'laravel-elastica-bridge';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
