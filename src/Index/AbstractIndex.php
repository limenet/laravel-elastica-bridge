<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Index;

use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastica\Document;
use Elastica\Exception\InvalidException;
use Elastica\Exception\NotFoundException;
use Elastica\Index;
use Elastica\Query;
use Elastica\ResultSet;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Enum\IndexBlueGreenSuffix;
use Limenet\LaravelElasticaBridge\Exception\Index\BlueGreenIndicesIncorrectlySetupException;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface;
use Limenet\LaravelElasticaBridge\Util\ElasticsearchResponse;
use RuntimeException;

abstract class AbstractIndex implements IndexInterface
{
    public function __construct(
        protected ElasticaClient $client
    ) {
        $this->client = $client;
    }

    public function getMapping(): array
    {
        return [];
    }

    public function getSettings(): array
    {
        return [];
    }

    final public function getCreateArguments(): array
    {
        $mapping = $this->getMapping();
        $mapping['properties'] ??= [];
        $mapping['properties'][self::DOCUMENT_MODEL_ID] ??= ['type' => 'keyword'];
        $mapping['properties'][self::DOCUMENT_MODEL_CLASS] ??= ['type' => 'keyword'];

        return array_filter([
            'mappings' => $mapping,
            'settings' => $this->getSettings(),
        ]);
    }

    public function getBatchSize(): int
    {
        return 5000;
    }

    public function getElasticaIndex(): Index
    {
        return $this->client->getIndex($this->getName());
    }

    /**
     * @param  int  $size  Max number of elements to be retrieved aka limit
     * @param  int  $from  Number of elements to skip from the beginning aka offset
     * @return Model[]
     */
    public function searchForElements(Query\AbstractQuery $query, int $size = 10, int $from = 0): array
    {
        return $this->documentResultToElements(
            $this->getElasticaIndex()
                ->search(
                    (new Query($query))
                        ->setSize($size)
                        ->setFrom($from)
                )
        );
    }

    /**
     * @return Model[]
     */
    public function documentResultToElements(ResultSet $result): array
    {
        $elements = [];
        foreach ($result->getDocuments() as $esDoc) {
            $elements[] = $this->getModelInstance($esDoc);
        }

        return $elements;
    }

    public function getModelInstance(Document $document): Model
    {
        try {
            $modelClass = $document->get(self::DOCUMENT_MODEL_CLASS);
            $modelId = $document->get(self::DOCUMENT_MODEL_ID);
        } catch (InvalidException) {
            throw new RuntimeException;
        }

        return $modelClass::findOrFail($modelId);
    }

    public function getDocumentInstance(Model|ElasticsearchableInterface $model): ?Document
    {
        try {
            return $this->getElasticaIndex()->getDocument($model->getElasticsearchId());
        } catch (NotFoundException) {
            return null;
        }
    }

    final public function hasBlueGreenIndices(): bool
    {
        return array_reduce(
            array_map(
                fn (IndexBlueGreenSuffix $suffix): bool => $this->client->getIndex($this->getName().$suffix->value)->exists(),
                IndexBlueGreenSuffix::cases()
            ),
            fn (bool $carry, bool $item): bool => $item && $carry,
            true
        );
    }

    final public function getBlueGreenActiveSuffix(): IndexBlueGreenSuffix
    {
        if (! $this->hasBlueGreenIndices()) {
            throw new BlueGreenIndicesIncorrectlySetupException;
        }

        try {
            $aliases = array_filter(
                ElasticsearchResponse::getResponse($this->getElasticaIndex()->getClient()->indices()->getAlias(['name' => $this->getName()]))->asArray(),
                fn (array $datum): bool => array_key_exists($this->getName(), $datum['aliases'])
            );
        } catch (ClientResponseException) {
            throw new BlueGreenIndicesIncorrectlySetupException;
        }

        if (count($aliases) !== 1) {
            throw new BlueGreenIndicesIncorrectlySetupException;
        }

        $suffix = substr(array_keys($aliases)[0], strlen($this->getName()));

        return IndexBlueGreenSuffix::tryFrom($suffix) ?? throw new BlueGreenIndicesIncorrectlySetupException;
    }

    final public function getBlueGreenInactiveSuffix(): IndexBlueGreenSuffix
    {
        return match ($this->getBlueGreenActiveSuffix()) {
            IndexBlueGreenSuffix::BLUE => IndexBlueGreenSuffix::GREEN,
            IndexBlueGreenSuffix::GREEN => IndexBlueGreenSuffix::BLUE,
        };
    }

    final public function getBlueGreenActiveElasticaIndex(): Index
    {
        return $this->client->getIndex($this->getName().$this->getBlueGreenActiveSuffix()->value);
    }

    final public function getBlueGreenInactiveElasticaIndex(): Index
    {
        return $this->client->getIndex($this->getName().$this->getBlueGreenInactiveSuffix()->value);
    }

    final public function indexingLock(): Lock
    {
        return Cache::lock(self::class.$this->getName());
    }

    public function ingoreIndexingErrors(): bool
    {
        return false;
    }
}
