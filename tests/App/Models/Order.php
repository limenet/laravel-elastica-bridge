<?php

namespace Limenet\LaravelElasticaBridge\Tests\App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableTrait;
use Limenet\LaravelElasticaBridge\Tests\Database\Factories\CustomerFactory;

class Order extends Model implements ElasticsearchableInterface
{
    use ElasticsearchableTrait;

    public function toElasticsearch(IndexInterface $indexConfig): array
    {
        return $this->toArray();
    }
}
