<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Jobs;

use Illuminate\Bus\Batchable;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Exception\Index\BlueGreenIndicesIncorrectlySetupException;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;

class SetupIndex extends AbstractIndexJob
{
    use Batchable;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected IndexInterface $indexConfig, protected bool $deleteExisting)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ElasticaClient $elastica): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        foreach (IndexInterface::INDEX_SUFFIXES as $suffix) {
            $name = $this->indexConfig->getName().$suffix;
            $aliasIndex = $elastica->getIndex($name);

            if ($this->deleteExisting && $aliasIndex->exists()) {
                $aliasIndex->delete();
            }

            if (! $aliasIndex->exists()) {
                $aliasIndex->create($this->indexConfig->getCreateArguments());
            }
        }

        try {
            $this->indexConfig->getBlueGreenActiveSuffix();
        } catch (BlueGreenIndicesIncorrectlySetupException $exception) {
            $elastica->getIndex($this->indexConfig->getName().IndexInterface::INDEX_SUFFIX_BLUE)->addAlias($this->indexConfig->getName());
        }
    }
}
