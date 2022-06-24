<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Jobs;

use Illuminate\Bus\Batchable;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;

class PopulateIndex extends AbstractIndexJob
{
    use Batchable;

    public function __construct(protected IndexInterface $indexConfig)
    {
    }

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $index = $this->indexConfig->getBlueGreenInactiveElasticaIndex();

        $index->delete();
        $index->create($this->indexConfig->getCreateArguments());

        $jobs = [];

        foreach ($this->indexConfig->getAllowedDocuments() as $indexDocument) {
            $modelCount = $indexDocument::count();

            for ($batchNumber = 0; $batchNumber < ceil($modelCount / $this->indexConfig->getBatchSize()); $batchNumber++) {
                $jobs[] = new PopulateBatchIndex(
                    $index,
                    $this->indexConfig,
                    $indexDocument,
                    $this->indexConfig->getBatchSize(),
                    $batchNumber * $this->indexConfig->getBatchSize()
                );
            }
        }

        $this->batch()?->add($jobs);

        $index->refresh();
    }
}
