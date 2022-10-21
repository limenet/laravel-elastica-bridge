<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Services;

use Elastica\Exception\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;

class ModelEventListener
{
    public const EVENT_CREATED = 'created';

    public const EVENT_UPDATED = 'updated';

    public const EVENT_SAVED = 'saved';

    public const EVENT_RESTORED = 'restored';

    public const EVENT_DELETED = 'deleted';

    public const EVENTS = [
        self::EVENT_CREATED,
        self::EVENT_UPDATED,
        self::EVENT_SAVED,
        self::EVENT_RESTORED,
        self::EVENT_DELETED,
    ];

    public function __construct(protected IndexRepository $indexRepository)
    {
    }

    /** @param  ElasticsearchableInterface&Model  $model */
    public function handle(string $event, Model $model): void
    {
        foreach ($this->matchingIndicesForElement($model) as $index) {
            if (! $index->getElasticaIndex()->exists()) {
                continue;
            }

            $shouldBePresent = true;

            if (! $model->shouldIndex($index) || $event === self::EVENT_DELETED) {
                $shouldBePresent = false;
            }

            $shouldBePresent
                ? $this->ensureModelPresentInIndex($index, $model)
                : $this->ensureModelMissingFromIndex($index, $model);
        }
    }

    /** @param  ElasticsearchableInterface&Model  $model */
    protected function ensureModelPresentInIndex(IndexInterface $index, Model $model): void
    {
        $index->getElasticaIndex()->addDocument($model->toElasticaDocument($index));
    }

    /** @param  ElasticsearchableInterface&Model  $model */
    protected function ensureModelMissingFromIndex(IndexInterface $index, Model $model): void
    {
        try {
            $index->getElasticaIndex()->deleteById($model->getElasticsearchId());
        } catch (NotFoundException) {
        }
    }

    /**
     * @return IndexInterface[]
     */
    public function matchingIndicesForElement(Model $model): array
    {
        return array_filter(
            $this->indexRepository->all(),
            fn (IndexInterface $index): bool => in_array($model::class, $index->getAllowedDocuments(), true)
        );
    }
}
