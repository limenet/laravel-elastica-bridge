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
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface;

class PopulateBatchIndex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected Index $index, protected string $indexDocument, protected int $limit, protected int $offset)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        $esDocuments = [];

        /** @var ElasticsearchableInterface[] $records */
        $records = $this->indexDocument::offset($this->offset)->limit($this->limit)->get();
        foreach ($records as $record) {
            if (!$record->shouldIndex()) {
                continue;
            }

            $esDocuments[] = $record->toElasticaDocument();
        }

        if (count($esDocuments) > 0) {
            $this->index->addDocuments($esDocuments);
            $esDocuments = [];
        }
    }
}
