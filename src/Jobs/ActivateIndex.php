<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Jobs;

use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;

class ActivateIndex extends AbstractIndexJob
{
    /**
     * @param  class-string<IndexInterface>  $indexConfigKey
     */
    public function __construct(
        protected string $indexConfigKey
    ) {}

    public function handle(IndexRepository $indexRepository): void
    {
        $indexConfig = $indexRepository->get($this->indexConfigKey);

        $newIndex = $indexConfig->getBlueGreenInactiveElasticaIndex();
        $newIndex->flush();
        $newIndex->addAlias($indexConfig->getName(), true);
    }
}
