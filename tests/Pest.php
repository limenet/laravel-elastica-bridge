<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Limenet\LaravelElasticaBridge\Tests\TestCase;

echo __LINE__."\n";
uses(TestCase::class)->in(__DIR__);
echo __LINE__."\n";
uses(RefreshDatabaseState::class);
echo __LINE__."\n";
