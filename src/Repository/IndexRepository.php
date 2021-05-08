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
    public function __construct(protected array $indices)
    {
    }

    /**
     * @return array<string,IndexInterface>
     */
    public function all(): array
    {
        return $this->indices;
    }

    public function get(string $key): IndexInterface
    {
        return $this->indices[$key];
    }
}
