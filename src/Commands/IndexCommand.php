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
        /** @var array<int,string> $indices */
        $indices = $this->argument('index');
        foreach ($this->indexRepository->all() as $indexConfig) {
            if (
                ! empty($indices)
                && ! in_array($indexConfig->getName(), $indices, true)
            ) {
                continue;
            }

            $this->info(sprintf('Indexing %s', $indexConfig->getName()));

            $lock = $indexConfig->indexingLock();

            /** @var bool $force */
            $force = $this->option('force');
            if ($force) {
                $lock->forceRelease();
            }

            if (! $lock->get()) {
                $this->warn('Not indexing as another job is still running.');

                continue;
            }

            $indexConfigKey = $indexConfig::class;

            /** @var bool $delete */
            $delete = $this->option('delete');

            Bus::batch([
                [
                    new SetupIndex($indexConfigKey, $delete),
                    new PopulateIndex($indexConfigKey),
                ],
            ])
                ->onConnection(config('elastica-bridge.connection'))
                ->then(function () use ($indexConfigKey): void {
                    ActivateIndex::dispatch($indexConfigKey)
                        ->onConnection(config('elastica-bridge.connection'));
                })
                ->catch(function () use ($indexConfigKey): void {
                    app(IndexRepository::class)->get($indexConfigKey)->indexingLock()->forceRelease();
                })
                ->finally(function () use ($indexConfigKey): void {
                    app(IndexRepository::class)->get($indexConfigKey)->indexingLock()->forceRelease();
                })
                ->name('ES index: '.$indexConfig->getName())
                ->dispatch();
        }

        return self::SUCCESS;
    }
}
