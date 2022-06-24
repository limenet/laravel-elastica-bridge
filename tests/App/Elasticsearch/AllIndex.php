<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch;

use Limenet\LaravelElasticaBridge\Index\AbstractIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Product;

class AllIndex extends AbstractIndex
{
    public function getName(): string
    {
        return 'testing_all';
    }

    public function getAllowedDocuments(): array
    {
        return [Customer::class, Order::class, Product::class];
    }
}
