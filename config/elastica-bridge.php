<?php

return [
    'elasticsearch' => [
        'host' => env('ELASTICSEARCH_HOST', 'localhost'),
        'port' => env('ELASTICSEARCH_PORT', '9200'),
    ],
    'indices' => [],
    'queue' => env('ELASTICSEARCH_QUEUE', config('queue.default')),
];
