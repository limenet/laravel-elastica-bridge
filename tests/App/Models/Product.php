<?php

namespace Limenet\LaravelElasticaBridge\Tests\App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableTrait;
use Limenet\LaravelElasticaBridge\Tests\Database\Factories\ProductFactory;

class Product extends Model implements ElasticsearchableInterface
{
    use HasFactory;
    use ElasticsearchableTrait;

    public function shouldIndex(IndexInterface $indexConfig): bool
    {
        return true;
    }

    protected static function newFactory(): Factory
    {
        return ProductFactory::new();
    }
}
