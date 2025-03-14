<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;
use Throwable;

class PopulateBatchIndex implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param  class-string<IndexInterface>  $indexConfigKey
     */
    public function __construct(
        protected string $indexConfigKey,
        private readonly string $indexDocument,
        private readonly int $limit,
        private readonly int $offset
    ) {}

    public function handle(IndexRepository $indexRepository): void
    {
        $indexConfig = $indexRepository->get($this->indexConfigKey);
        if ($this->batch()?->cancelled() === true) {
            return;
        }

        $esDocuments = [];

        /** @var ElasticsearchableInterface[] $records */
        $records = $this->indexDocument::offset($this->offset)->limit($this->limit)->get();
        foreach ($records as $record) {
            if (! $record->shouldIndex($indexConfig)) {
                continue;
            }

            $esDocuments[] = $record->toElasticaDocument($indexConfig);
        }

        if ($esDocuments === []) {
            return;
        }

        try {
            $indexConfig->getBlueGreenInactiveElasticaIndex()->addDocuments($esDocuments);
        } catch (Throwable $throwable) {
            if (! $indexConfig->ingoreIndexingErrors()) {
                throw $throwable;
            }
        }
    }
}
