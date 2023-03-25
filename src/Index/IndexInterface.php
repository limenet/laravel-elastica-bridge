<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Index;

use Elastica\Document;
use Elastica\Index;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Database\Eloquent\Model;
use Limenet\LaravelElasticaBridge\Exception\Index\BlueGreenIndicesIncorrectlySetupException;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface;

interface IndexInterface
{
    /**
     * The suffix for the blue index.
     */
    public const INDEX_SUFFIX_BLUE = '--blue';

    /**
     * The suffix for the green index.
     */
    public const INDEX_SUFFIX_GREEN = '--green';

    /**
     * List of valid index suffixes.
     */
    public const INDEX_SUFFIXES = [self::INDEX_SUFFIX_BLUE, self::INDEX_SUFFIX_GREEN];

    public const DOCUMENT_MODEL_ID = '__id';

    public const DOCUMENT_MODEL_CLASS = '__class';

    /**
     * The name of the Elasticsearch index.
     */
    public function getName(): string;

    /**
     * The number of Pimcore elements to be stored in the index in one batch.
     * This is used e.g. when populating the index.
     *
     * @see IndexCommand
     */
    public function getBatchSize(): int;

    /**
     * Defines the mapping to be used for this index.
     * Passed 1:1 to Elasticsearch.
     *
     * @return array<array>
     */
    public function getMapping(): array;

    /**
     * Defines the settings to be used for this index.
     * Passed 1:1 to Elasticsearch.
     *
     * @return array<array>
     */
    public function getSettings(): array;

    /**
     * @return array{mappings:non-empty-array<mixed>,settings?:array<mixed>}
     *
     * @internal
     */
    public function getCreateArguments(): array;

    /**
     * Defines the types of documents found in this index. Array of classes implementing ElasticsearchableInterface.
     *
     * @return array<string> Class names of Model classes
     */
    public function getAllowedDocuments(): array;

    /**
     * Exposes a pre-configured Elastica client for this index.
     */
    public function getElasticaIndex(): Index;

    /**
     * Checks whether the blue and green indices are correctly set up.
     *
     * @internal
     */
    public function hasBlueGreenIndices(): bool;

    /**
     * Returns the currently active blue/green suffix.
     *
     * @throws BlueGreenIndicesIncorrectlySetupException
     *
     * @internal
     */
    public function getBlueGreenActiveSuffix(): string;

    /**
     * Returns the currently inactive blue/green suffix.
     *
     * @throws BlueGreenIndicesIncorrectlySetupException
     *
     * @internal
     */
    public function getBlueGreenInactiveSuffix(): string;

    /**
     * Returns the currently active blue/green Elastica index.
     *
     * @throws BlueGreenIndicesIncorrectlySetupException
     *
     * @see IndexInterface::getElasticaIndex()
     *
     * @internal
     */
    public function getBlueGreenActiveElasticaIndex(): Index;

    /**
     * Returns the currently inactive blue/green Elastica index.
     *
     * @throws BlueGreenIndicesIncorrectlySetupException
     *
     * @see IndexInterface::getElasticaIndex()
     *
     * @internal
     */
    public function getBlueGreenInactiveElasticaIndex(): Index;

    /**
     * Given an Elastica document, return the corresponding Laravel model.
     */
    public function getModelInstance(Document $document): Model;

    /**
     * Given a Laravel model, return the corresponding Elastica document.
     *
     * @return Document
     */
    public function getDocumentInstance(Model|ElasticsearchableInterface $model): ?Document;

    public function indexingLock(): Lock;

    /**
     * Should errors during indexing be ignored?
     * Recommendation: set getBatchSize() to 1 if this is enabled.
     */
    public function ingoreIndexingErrors(): bool;
}
