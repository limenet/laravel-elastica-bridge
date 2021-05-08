<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Model;

use Elastica\Document;

interface ElasticsearchableInterface
{
    public function getModel(): string;

    /**
     * @internal
     */
    public function getElasticsearchId(): string;

    public function toElasticsearch(): array;

    public function shouldIndex(): bool;

    public function toElasticaDocument(): Document;
}
