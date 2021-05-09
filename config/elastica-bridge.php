<?php

return [
    'elasticsearch' => [
        'host' => env('ELASTICSEARCH_HOST', 'localhost'),
        'port' => env('ELASTICSEARCH_PORT', '9200'),
    ],
    'indices' => [],
    'connection' => env('ELASTICSEARCH_QUEUE_CONNECTION', config('queue.default')),
];
