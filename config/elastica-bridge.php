<?php

return [
    'elasticsearch' => [
        'host' => env('ELASTICSEARCH_HOST', 'localhost'),
        'port' => env('ELASTICSEARCH_PORT', '9200'),
    ],
    'indices' => [],
    'connection' => env('ELASTICSEARCH_QUEUE_CONNECTION', config('queue.default')),
    'events' => [
        'listen' => true,
    ],
    'logging' => [
        'sentry_breadcrumbs' => false,
        // the least severe PSR-3 level which is still logged; Elasticsearch logs
        // the full request and response bodies at 'debug'
        'level' => env('ELASTICSEARCH_LOG_LEVEL', 'debug'),
    ],
];
