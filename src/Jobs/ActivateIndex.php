<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Jobs;

use Limenet\LaravelElasticaBridge\Index\IndexInterface;

class ActivateIndex extends AbstractIndexJob
{
    public function __construct(protected IndexInterface $indexConfig)
    {
    }

    public function handle(): void
    {
        $oldIndex = $this->indexConfig->getBlueGreenActiveElasticaIndex();
        $newIndex = $this->indexConfig->getBlueGreenInactiveElasticaIndex();

        $newIndex->flush();
        $oldIndex->removeAlias($this->indexConfig->getName());
        $newIndex->addAlias($this->indexConfig->getName());
        $oldIndex->flush();
    }
}
