<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Jobs;

use Illuminate\Bus\Batchable;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;

class PopulateIndex extends AbstractIndexJob
{
    use Batchable;

    /**
     * @param  class-string<IndexInterface>  $indexConfigKey
     */
    public function __construct(
        protected string $indexConfigKey
    ) {}

    public function handle(IndexRepository $indexRepository): void
    {
        $indexConfig = $indexRepository->get($this->indexConfigKey);

        if ($this->batch()?->cancelled() === true) {
            return;
        }

        $index = $indexConfig->getBlueGreenInactiveElasticaIndex();

        $index->delete();
        $index->create($indexConfig->getCreateArguments());

        $jobs = [];

        foreach ($indexConfig->getAllowedDocuments() as $indexDocument) {
            $modelCount = $indexDocument::count();

            for ($batchNumber = 0; $batchNumber < ceil($modelCount / $indexConfig->getBatchSize()); $batchNumber++) {
                $jobs[] = new PopulateBatchIndex(
                    $indexConfig::class,
                    $indexDocument,
                    $indexConfig->getBatchSize(),
                    $batchNumber * $indexConfig->getBatchSize()
                );
            }
        }

        $this->batch()?->add($jobs);

        $index->refresh();
    }
}
