<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;

abstract class AbstractIndexJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected IndexInterface $indexConfig;

    public function uniqueId(): string
    {
        return $this->indexConfig::class;
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping($this->uniqueId()))
                ->expireAfter(now()->addMinutes(30)),
        ];
    }
}
