<?php

namespace Limenet\LaravelElasticaBridge\Tests\App\Models;

use Illuminate\Database\Eloquent\Model;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableTrait;

class Order extends Model implements ElasticsearchableInterface
{
    use ElasticsearchableTrait;

    public function toElasticsearch(IndexInterface $indexConfig): array
    {
        return $this->toArray();
    }
}
