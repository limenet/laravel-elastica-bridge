<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch;

use Limenet\LaravelElasticaBridge\Index\AbstractIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Order;

class OrderIndex extends AbstractIndex
{
    public function getName(): string
    {
        return 'testing_order';
    }

    public function getAllowedDocuments(): array
    {
        return [Order::class];
    }
}
