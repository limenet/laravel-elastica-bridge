<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Limenet\LaravelElasticaBridge\Jobs\ActivateIndex;
use Limenet\LaravelElasticaBridge\Jobs\PopulateIndex;
use Limenet\LaravelElasticaBridge\Jobs\SetupIndex;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;

class IndexCommand extends Command
{
    protected $signature = 'elastica-bridge:index {index?*} {--delete} {--force}';

    protected $description = 'Re-create the ES index and populate with data';

    public function __construct(
        private readonly IndexRepository $indexRepository
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        foreach ($this->indexRepository->all() as $indexConfig) {
            if (
                ! empty($this->argument('index'))
                && ! in_array($indexConfig->getName(), $this->argument('index'), true)
            ) {
                continue;
            }

            $this->info(sprintf('Indexing %s', $indexConfig->getName()));

            $lock = $indexConfig->indexingLock();

            if ((bool) $this->option('force')) {
                $lock->forceRelease();
            }

            if (! $lock->get()) {
                $this->warn('Not indexing as another job is still running.');

                continue;
            }

            Bus::batch([
                [
                    new SetupIndex($indexConfig, (bool) $this->option('delete')),
                    new PopulateIndex($indexConfig),
                ],
            ])
                ->onConnection(config('elastica-bridge.connection'))
                ->then(function () use ($indexConfig): void {
                    ActivateIndex::dispatch($indexConfig)
                        ->onConnection(config('elastica-bridge.connection'));
                })
                ->catch(function () use ($indexConfig): void {
                    $indexConfig->indexingLock()->forceRelease();
                })
                ->finally(function () use ($indexConfig): void {
                    $indexConfig->indexingLock()->forceRelease();
                })
                ->name('ES index: '.$indexConfig->getName())
                ->dispatch();
        }

        return self::SUCCESS;
    }
}
