<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch;

use Limenet\LaravelElasticaBridge\Index\AbstractIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Models\Invoice;

class InvoiceIndex extends AbstractIndex
{
    public function getName(): string
    {
        return 'testing_invoice';
    }

    public function getAllowedDocuments(): array
    {
        return [Invoice::class];
    }
}
