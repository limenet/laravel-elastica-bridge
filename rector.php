<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        naming: false,
        instanceOf: true,
        earlyReturn: true,
        strictBooleans: true,
        carbon: true,
        phpunitCodeQuality: true,
    )
    ->withSets([
        LaravelSetList::LARAVEL_110,
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
