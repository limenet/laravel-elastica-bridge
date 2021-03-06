<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Jobs;

use Elastica\Index;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface;

class PopulateBatchIndex implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected Index $index,
        protected IndexInterface $indexConfig,
        protected string $indexDocument,
        protected int $limit,
        protected int $offset
    ) {
    }

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $esDocuments = [];

        /** @var ElasticsearchableInterface[] $records */
        $records = $this->indexDocument::offset($this->offset)->limit($this->limit)->get();
        foreach ($records as $record) {
            if (!$record->shouldIndex($this->indexConfig)) {
                continue;
            }

            $esDocuments[] = $record->toElasticaDocument($this->indexConfig);
        }

        if (count($esDocuments) === 0) {
            return;
        }

        $this->index->addDocuments($esDocuments);
    }
}
