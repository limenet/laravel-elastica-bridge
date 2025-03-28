<?php

namespace Limenet\LaravelElasticaBridge\Events;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Jobs\ModelEvent;
use Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface;

class EventHandler
{
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            collect(ModelEvent::EVENTS)
                ->map(fn (string $name): string => sprintf('eloquent.%s:*', $name))
                ->toArray(),
            function ($event, $models): void {
                if (! ElasticaClient::listensToEvents()) {
                    return;
                }

                $name = str($event)->before(':')->after('.')->toString();

                collect($models)
                    ->filter(fn (Model $model): bool => $model instanceof ElasticsearchableInterface)
                    ->each(fn (Model $model) => ModelEvent::dispatch(
                        $name,
                        $model::class,
                        $model->getKeyName(),
                        $model->getKey(),
                        $model->getElasticsearchId()
                    ));
            });
    }
}
