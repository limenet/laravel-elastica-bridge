<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Model;

use Elastica\Document;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;

trait ElasticsearchableTrait
{
    final public function getElasticsearchId(): string
    {
        return str($this::class)
            ->replace('\\', ' ')
            ->snake()
            ->append('-', $this->getKey())
            ->toString();
    }

    public function toElasticsearch(IndexInterface $indexConfig): array
    {
        return $this->toArray();
    }

    public function shouldIndex(IndexInterface $indexConfig): bool
    {
        return true;
    }

    public function toElasticaDocument(IndexInterface $indexConfig): Document
    {
        return new Document(
            $this->getElasticsearchId(),
            [
                ...$this->toElasticsearch($indexConfig),
                IndexInterface::DOCUMENT_MODEL_CLASS => $this::class,
                IndexInterface::DOCUMENT_MODEL_ID => $this->getKey(),
            ]
        );
    }
}
