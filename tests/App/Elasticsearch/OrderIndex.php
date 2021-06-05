<?php


namespace Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch;

use Limenet\LaravelElasticaBridge\Index\AbstractIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;
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
