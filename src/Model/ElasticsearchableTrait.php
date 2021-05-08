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

    public function toElasticaDocument(): Document
    {
        return new Document(
            $this->getElasticsearchId(),
            array_merge(
                $this->toElasticsearch(),
                [
                     IndexInterface::DOCUMENT_MODEL_CLASS => $this::class,
                     IndexInterface::DOCUMENT_MODEL_ID => $this->id,
                 ]
            )
        );
    }
}
