<?php


namespace Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch;

use Limenet\LaravelElasticaBridge\Index\AbstractIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Product;

class ProductIndex extends AbstractIndex
{
    public function getName(): string
    {
        return 'testing_product';
    }

    public function getAllowedDocuments(): array
    {
        return [Customer::class, Product::class];
    }
}
