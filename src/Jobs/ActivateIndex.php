<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Jobs;

use Limenet\LaravelElasticaBridge\Index\IndexInterface;

class ActivateIndex extends AbstractIndexJob
{
    public function __construct(
        protected IndexInterface $indexConfig
    ) {
    }

    public function handle(): void
    {
        $newIndex = $this->indexConfig->getBlueGreenInactiveElasticaIndex();
        $newIndex->flush();
        $newIndex->addAlias($this->indexConfig->getName(), true);
    }
}
