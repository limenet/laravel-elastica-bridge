<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Jobs;

use Elastica\Exception\ClientException;
use Elastica\Exception\ConnectionException;
use Elastica\Exception\ResponseException;
use Illuminate\Bus\Batchable;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Exception\Index\BlueGreenIndicesIncorrectlySetupException;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;

class SetupIndex extends AbstractIndexJob
{
    use Batchable;

    public function __construct(
        protected IndexInterface $indexConfig,
        private readonly bool $deleteExisting
    ) {
    }

    public function handle(ElasticaClient $elastica): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }
        $this->migrate($elastica);
        $this->cleanup($elastica);
        $this->setup($elastica);
    }

    private function migrate(ElasticaClient $elastica): void
    {
        if (! $this->indexConfig->hasBlueGreenIndices()) {
            return;
        }

        $index = $elastica->getClient()->getIndex($this->indexConfig->getName());

        try {
            $response = $elastica->getClient()->request(sprintf('_alias/%s', $this->indexConfig->getName()));
        } catch (ClientException|ConnectionException|ResponseException) {
            if ($index->exists() && count($index->getAliases()) === 0) {
                $index->delete();
            }

            return;
        }

        if ($response->hasError()) {
            return;
        }

        if (array_keys($response->getData())[0] !== $this->indexConfig->getName()) {
            return;
        }

        if ($index->exists()) {
            $index->delete();
        }
    }

    private function cleanup(ElasticaClient $elastica): void
    {
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
    }

    private function setup(ElasticaClient $elastica): void
    {
        try {
            $this->indexConfig->getBlueGreenActiveSuffix();
        } catch (BlueGreenIndicesIncorrectlySetupException) {
            $elastica->getIndex($this->indexConfig->getName().IndexInterface::INDEX_SUFFIX_BLUE)->addAlias($this->indexConfig->getName());
        }
    }
}
