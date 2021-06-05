<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Jobs\ActivateIndex;
use Limenet\LaravelElasticaBridge\Jobs\PopulateIndex;
use Limenet\LaravelElasticaBridge\Jobs\SetupIndex;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;

class IndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elastica-bridge:index {index?*} {--delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-create the ES index and populate with data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(protected ElasticaClient $elastica, protected IndexRepository $indexRepository)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach ($this->indexRepository->all() as $indexConfig) {
            if (
                ! empty($this->argument('index'))
                && ! in_array($indexConfig->getName(), $this->argument('index'), true)
            ) {
                continue;
            }

            $this->info(sprintf('Indexing %s', $indexConfig->getName()));

            Bus::batch([
                    [new SetupIndex($indexConfig, (bool) $this->option('delete'))],
                    [new PopulateIndex($indexConfig)],
                ])
                ->onConnection(config('elastica-bridge.connection'))
                ->then(function () use ($indexConfig): void {
                    ActivateIndex::dispatch($indexConfig)
                        ->onConnection(config('elastica-bridge.connection'));
                })
                ->name('ES index: '.$indexConfig->getName())
                ->dispatch();
        }

        return 0;
    }
}
