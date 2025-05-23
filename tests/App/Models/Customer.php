<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableTrait;
use Limenet\LaravelElasticaBridge\Tests\Database\Factories\CustomerFactory;

class Customer extends Model implements ElasticsearchableInterface
{
    use ElasticsearchableTrait;

    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    public function toElasticsearch(IndexInterface $indexConfig): array
    {
        return $this->toArray();
    }

    public function shouldIndex(IndexInterface $indexConfig): bool
    {
        return ($this->id % 2) === 0;
    }

    /**
     * @return Factory<self>
     */
    protected static function newFactory(): Factory
    {
        return CustomerFactory::new();
    }
}
