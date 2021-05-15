<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Model;

use Elastica\Document;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;

trait ElasticsearchableTrait
{
    final public function getModel(): string
    {
        return __CLASS__;
    }

    final public function getElasticsearchId(): string
    {
        return $this::class.'|'.$this->id;
    }
    public function toElasticsearch(IndexInterface $indexConfig): array{
        return $this->toArray();
    }

    public function toElasticaDocument(IndexInterface $indexConfig): Document
    {
        return new Document(
            $this->getElasticsearchId(),
            array_merge(
                $this->toElasticsearch($indexConfig),
                [
                     IndexInterface::DOCUMENT_MODEL_CLASS => $this::class,
                     IndexInterface::DOCUMENT_MODEL_ID => $this->id,
                 ]
            )
        );
    }
}
