<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Exception\Command;

use Limenet\LaravelElasticaBridge\Exception\BaseException;
use Throwable;

class IndexingFailedException extends BaseException
{
    public function __construct(?Throwable $previous)
    {
        parent::__construct('Indexing command failed', 0, $previous);
    }
}
