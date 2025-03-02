<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableTrait;
use Limenet\LaravelElasticaBridge\Tests\Database\Factories\ProductFactory;

class Product extends Model implements ElasticsearchableInterface
{
    use ElasticsearchableTrait;

    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * @return Factory<self>
     */
    protected static function newFactory(): Factory
    {
        return ProductFactory::new();
    }
}
