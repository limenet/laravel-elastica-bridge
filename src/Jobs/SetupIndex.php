<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Jobs;

use Illuminate\Bus\Batchable;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Enum\IndexBlueGreenSuffix;
use Limenet\LaravelElasticaBridge\Exception\Index\BlueGreenIndicesIncorrectlySetupException;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;
use Limenet\LaravelElasticaBridge\Util\ElasticsearchResponse;

class SetupIndex extends AbstractIndexJob
{
    private IndexInterface $indexConfig;

    use Batchable;

    /**
     * @param  class-string<IndexInterface>  $indexConfigKey
     */
    public function __construct(
        protected string $indexConfigKey,
        private readonly bool $deleteExisting
    ) {}

    public function handle(ElasticaClient $elastica, IndexRepository $indexRepository): void
    {
        if ($this->batch()?->cancelled() === true) {
            return;
        }

        $this->indexConfig = $indexRepository->get($this->indexConfigKey);
        $this->migrate($elastica);
        $this->cleanup($elastica);
        $this->setup($elastica);
    }

    private function migrate(ElasticaClient $elastica): void
    {
        if (! $this->indexConfig->hasBlueGreenIndices()) {
            return;
        }

        $nonAliasIndex = $elastica->getClient()->getIndex($this->indexConfig->getName());

        if (
            $nonAliasIndex->exists()
            && ! ElasticsearchResponse::getResponse($elastica->getClient()->indices()->existsAlias(['name' => $this->indexConfig->getName()]))->asBool()
        ) {
            $nonAliasIndex->delete();
        }
    }

    private function cleanup(ElasticaClient $elastica): void
    {
        foreach (IndexBlueGreenSuffix::cases() as $suffix) {
            $name = $this->indexConfig->getName().$suffix->value;
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
            $elastica->getIndex($this->indexConfig->getName().IndexBlueGreenSuffix::BLUE->value)->addAlias($this->indexConfig->getName());
        }
    }
}
