<?php

namespace Limenet\LaravelElasticaBridge\Tests\App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableTrait;
use Limenet\LaravelElasticaBridge\Tests\Database\Factories\ProductFactory;

class Product extends Model implements ElasticsearchableInterface
{
    use HasFactory;
    use ElasticsearchableTrait;

    protected static function newFactory(): Factory
    {
        return ProductFactory::new();
    }
}
