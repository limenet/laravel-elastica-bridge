<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Repository;

use Limenet\LaravelElasticaBridge\Index\IndexInterface;

/**
 * Used for typehinting. Contains an array of all IndexInterface implementations.
 *
 * @see IndexInterface
 */
class IndexRepository
{
    /**
     * @var array<class-string<IndexInterface>,IndexInterface>
     */
    private array $indices;

    /**
     * @param  IndexInterface[]  $indices
     */
    public function __construct(array $indices)
    {
        foreach ($indices as $index) {
            $this->indices[$index::class] = $index;
        }
    }

    /**
     * @return array<class-string<IndexInterface>,IndexInterface>
     */
    public function all(): array
    {
        return $this->indices;
    }

    /**
     * @param  class-string<IndexInterface>  $key
     */
    public function get(string $key): IndexInterface
    {
        return $this->indices[$key];
    }
}
