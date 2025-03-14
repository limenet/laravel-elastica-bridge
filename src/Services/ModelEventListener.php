<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Services;

use Elastic\Elasticsearch\Exception\ClientResponseException;
use Illuminate\Database\Eloquent\Model;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;

class ModelEventListener
{
    private const EVENT_CREATED = 'created';

    private const EVENT_UPDATED = 'updated';

    private const EVENT_SAVED = 'saved';

    private const EVENT_RESTORED = 'restored';

    private const EVENT_DELETED = 'deleted';

    final public const EVENTS = [
        self::EVENT_CREATED,
        self::EVENT_UPDATED,
        self::EVENT_SAVED,
        self::EVENT_RESTORED,
        self::EVENT_DELETED,
    ];

    public function handle(string $event, Model $model): void
    {
        if (! $model instanceof ElasticsearchableInterface) {
            return;
        }

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
    private function ensureModelPresentInIndex(IndexInterface $index, Model $model): void
    {
        $index->getElasticaIndex()->addDocument($model->toElasticaDocument($index));
    }

    /** @param  ElasticsearchableInterface&Model  $model */
    private function ensureModelMissingFromIndex(IndexInterface $index, Model $model): void
    {
        try {
            $index->getElasticaIndex()->deleteById($model->getElasticsearchId());
        } catch (ClientResponseException) {
        }
    }

    /**
     * @return IndexInterface[]
     */
    public function matchingIndicesForElement(Model $model): array
    {
        return array_filter(
            app(IndexRepository::class)->all(),
            fn (IndexInterface $index): bool => in_array($model::class, $index->getAllowedDocuments(), true)
        );
    }
}
