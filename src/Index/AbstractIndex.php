<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Index;

use Elastica\Document;
use Elastica\Exception\NotFoundException;
use Elastica\Index;
use Elastica\Query;
use Elastica\ResultSet;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Exception\Index\BlueGreenIndicesIncorrectlySetupException;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface;
use RuntimeException;

abstract class AbstractIndex implements IndexInterface
{
    public function __construct(protected ElasticaClient $client)
    {
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

    final public function hasMapping(): bool
    {
        return count($this->getMapping()) > 0;
    }

    final public function getCreateArguments(): array
    {
        return array_filter([
            'mappings' => $this->getMapping(),
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
     * @param int $size Max number of elements to be retrieved aka limit
     * @param int $from Number of elements to skip from the beginning aka offset
     *
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
        $id = $document->getId();

        if (empty($id)) {
            throw new RuntimeException();
        }

        [$modelClass,$modelId] = explode('|', $id);

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
                fn (string $suffix): bool => $this->client->getIndex($this->getName().$suffix)->exists(),
                self::INDEX_SUFFIXES
            ),
            fn (bool $carry, bool $item): bool => $item && $carry,
            true
        );
    }

    final public function getBlueGreenActiveSuffix(): string
    {
        if (!$this->hasBlueGreenIndices()) {
            throw new BlueGreenIndicesIncorrectlySetupException();
        }

        $aliases = array_filter(
            $this->client->getClient()->request('_aliases')->getData(),
            fn (array $datum): bool => in_array($this->getName(), array_keys($datum['aliases']), true)
        );

        if (count($aliases) !== 1) {
            throw new BlueGreenIndicesIncorrectlySetupException();
        }

        $suffix = substr((string) array_keys($aliases)[0], strlen($this->getName()));

        if (!in_array($suffix, self::INDEX_SUFFIXES, true)) {
            throw new BlueGreenIndicesIncorrectlySetupException();
        }

        return $suffix;
    }

    final public function getBlueGreenInactiveSuffix(): string
    {
        $active = $this->getBlueGreenActiveSuffix();

        if ($active === self::INDEX_SUFFIX_BLUE) {
            return self::INDEX_SUFFIX_GREEN;
        }

        if ($active === self::INDEX_SUFFIX_GREEN) {
            return self::INDEX_SUFFIX_BLUE;
        }

        throw new BlueGreenIndicesIncorrectlySetupException();
    }

    final public function getBlueGreenActiveElasticaIndex(): Index
    {
        return $this->client->getIndex($this->getName().$this->getBlueGreenActiveSuffix());
    }

    final public function getBlueGreenInactiveElasticaIndex(): Index
    {
        return $this->client->getIndex($this->getName().$this->getBlueGreenInactiveSuffix());
    }

    final public function indexingLock(): Lock
    {
        return Cache::lock(__CLASS__.$this->getName());
    }
}
