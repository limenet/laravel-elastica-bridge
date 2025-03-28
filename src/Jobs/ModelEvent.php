<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Jobs;

use Elastic\Elasticsearch\Exception\ClientResponseException;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;

class ModelEvent implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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

    /**
     * @template TModel of ElasticsearchableInterface&Model
     *
     * @param  class-string<TModel>  $modelClass
     * @param  model-property<TModel>  $keyName
     * @return void
     */
    public function __construct(
        private readonly string $event,
        private readonly string $modelClass,
        private readonly string $keyName,
        private readonly mixed $keyValue,
        private readonly string $elasticsearchId,
    ) {
        $this->onConnection(config('elastica-bridge.connection'));
    }

    public function handle(): void
    {
        $model = $this->modelClass::query()->where($this->keyName, $this->keyValue)->first();

        if (! $model instanceof ElasticsearchableInterface || ! $model instanceof Model) {
            foreach ($this->matchingIndicesForElement() as $index) {
                if (! $index->getElasticaIndex()->exists()) {
                    continue;
                }

                $this->ensureModelMissingFromIndex($index);
            }

            return;
        }

        foreach ($this->matchingIndicesForElement() as $index) {
            if (! $index->getElasticaIndex()->exists()) {
                continue;
            }

            $shouldBePresent = true;

            if (! $model->shouldIndex($index) || $this->event === self::EVENT_DELETED) {
                $shouldBePresent = false;
            }

            $shouldBePresent
                ? $this->ensureModelPresentInIndex($index, $model)
                : $this->ensureModelMissingFromIndex($index);
        }
    }

    /** @param  ElasticsearchableInterface&Model  $model */
    private function ensureModelPresentInIndex(IndexInterface $index, Model $model): void
    {
        $index->getElasticaIndex()->addDocument($model->toElasticaDocument($index));
    }

    private function ensureModelMissingFromIndex(IndexInterface $index): void
    {
        try {
            $index->getElasticaIndex()->deleteById($this->elasticsearchId);
        } catch (ClientResponseException) {
        }
    }

    /**
     * @return IndexInterface[]
     */
    public function matchingIndicesForElement(): array
    {
        return array_filter(
            app(IndexRepository::class)->all(),
            fn (IndexInterface $index): bool => in_array($this->modelClass, $index->getAllowedDocuments(), true)
        );
    }
}
