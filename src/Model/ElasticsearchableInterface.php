<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Model;

use Elastica\Document;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;

interface ElasticsearchableInterface
{
    public function getModel(): string;

    /**
     * @internal
     */
    public function getElasticsearchId(): string;

    public function toElasticsearch(IndexInterface $indexConfig): array;

    public function shouldIndex(IndexInterface $indexConfig): bool;

    public function toElasticaDocument(IndexInterface $indexConfig): Document;
}
