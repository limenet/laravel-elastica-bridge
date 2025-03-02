<?php

namespace Limenet\LaravelElasticaBridge\Events;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Services\ModelEventListener;

class EventHandler
{
    public function __construct(
        private readonly ElasticaClient $elasticaClient,
        private readonly ModelEventListener $modelEventListener
    ) {}

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            collect(ModelEventListener::EVENTS)
                ->map(fn (string $name): string => sprintf('eloquent.%s:*', $name))
                ->toArray(),
            function ($event, $models) {
                if (! $this->elasticaClient->listensToEvents()) {
                    return;
                }

                dispatch(function () use ($event, $models): void {
                    $name = (str($event)->before(':')->after('.'));
                    if (! $this->elasticaClient->listensToEvents()) {
                        return;
                    }

                    collect($models)->each(fn (Model $model) => $this->modelEventListener->handle($name, $model));
                })->onConnection(config('elastica-bridge.connection'));
            });
    }
}
