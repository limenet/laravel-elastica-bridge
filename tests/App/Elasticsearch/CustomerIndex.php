<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch;

use Limenet\LaravelElasticaBridge\Index\AbstractIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Customer;

class CustomerIndex extends AbstractIndex
{
    public function getName(): string
    {
        return 'testing_customer';
    }

    public function getAllowedDocuments(): array
    {
        return [Customer::class];
    }

    public function getMapping(): array
    {
        return [
            'properties' => [
                'group' => [
                    'type' => 'keyword',
                ],
            ],
        ];
    }
}
