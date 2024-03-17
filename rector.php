<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPreparedSets()
    ->withSets([
        LaravelSetList::LARAVEL_100,
    ])
    ->withPhpSets()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withRootFiles()
    ->withSkip([
        ReadOnlyPropertyRector::class => [
            'src/Client/ElasticaClient.php',
        ],
        StringClassNameToClassConstantRector::class => [
            'src/Client/ElasticaClient.php',
        ],
    ]);
