<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Commands;

use Illuminate\Console\Command;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;

class DeleteCommand extends Command
{
    protected $signature = 'elastica-bridge:delete {--force}';

    protected $description = 'Delete all Elasticsearch indices known to the package';

    public function __construct(
        private readonly ElasticaClient $elastica,
        private readonly IndexRepository $indexRepository
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('Do you want to proceed? (y/N)', false)) {
            return self::SUCCESS;
        }

        foreach ($this->inidices() as $index) {
            $client = $this->elastica->getIndex($index);
            if (! $client->exists()) {
                continue;
            }

            $this->info($index);

            foreach ($client->getAliases() as $alias) {
                $this->info($alias);
                $client->removeAlias($alias);
            }

            $client->delete();
        }

        return self::SUCCESS;
    }

    /**
     * @return string[]
     */
    private function inidices(): array
    {
        $indices = [];

        foreach ($this->indexRepository->all() as $indexConfig) {
            if ($indexConfig->hasBlueGreenIndices()) {
                $indices[] = $indexConfig->getBlueGreenActiveElasticaIndex()->getName();
                $indices[] = $indexConfig->getBlueGreenInactiveElasticaIndex()->getName();

                continue;
            }

            $indices[] = $indexConfig->getName();
        }

        return $indices;
    }
}
