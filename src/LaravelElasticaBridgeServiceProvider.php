<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge;

use Illuminate\Database\Eloquent\Model;
use function Illuminate\Events\queueable;
use Illuminate\Support\Facades\Event;
use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Commands\IndexCommand;
use Limenet\LaravelElasticaBridge\Commands\StatusCommand;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;
use Limenet\LaravelElasticaBridge\Services\ModelEventListener;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelElasticaBridgeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-elastica-bridge')
            ->hasConfigFile()
            ->hasCommands([IndexCommand::class, StatusCommand::class]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ElasticaClient::class);
        $this->app->bind(ModelEventListener::class);
        $this->app->tag(config('elastica-bridge.indices'), 'elasticaBridgeIndices');

        $this->app->when(IndexRepository::class)
                ->needs('$indices')
                ->giveTagged('elasticaBridgeIndices');
    }

    public function packageBooted(): void
    {
        foreach (ModelEventListener::EVENTS as $name) {
            Event::listen(
                sprintf('eloquent.%s:*', $name),
                queueable(function (string $event, array $models) use ($name): void {
                    if (!resolve(ElasticaClient::class)->listensToEvents()) {
                        return;
                    }

                    $modelEventListener = resolve(ModelEventListener::class);
                    collect($models)->each(fn (Model $model) => $modelEventListener->handle($name, $model));
                })->onConnection(config('elastica-bridge.connection'))
            );
        }
    }
}
