<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\Feature;

use Elastica\Query\BoolQuery;
use Elastica\Query\MatchQuery;
use Limenet\LaravelElasticaBridge\Index\IndexInterface;

class QueryTest extends TestCase
{
    public function test_get_by_id(): void
    {
        $id = 17;
        $this->index($this->productIndex);
        $elements = $this->productIndex->searchForElements(new MatchQuery(IndexInterface::DOCUMENT_MODEL_ID, $id));

        $this->assertCount(1, $elements);
        $this->assertSame(17, $elements[0]->id);
    }

    public function test_size_and_from(): void
    {
        $this->index($this->productIndex);

        $elements1 = $this->productIndex->searchForElements(new BoolQuery(), 5, 0);
        $this->assertCount(5, $elements1);

        $elements2 = $this->productIndex->searchForElements(new BoolQuery(), 5, 5);
        $this->assertCount(5, $elements2);
        $this->assertEmpty(collect($elements1)->map->id->intersect(collect($elements2)->map->id)->toArray());
    }
}
