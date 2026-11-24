<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;

abstract class AbstractIndexJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    /**
     * @var class-string<IndexInterface>
     */
    protected string $indexConfigKey;

    public function uniqueId(): string
    {
        return $this->indexConfigKey;
    }

    /**
     * @return array<int,object>
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping($this->uniqueId()))
                ->expireAfter(now()->addMinutes(30)),
        ];
    }
}
