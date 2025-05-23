<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge;

use Limenet\LaravelElasticaBridge\Client\ElasticaClient;
use Limenet\LaravelElasticaBridge\Commands\DeleteCommand;
use Limenet\LaravelElasticaBridge\Commands\IndexCommand;
use Limenet\LaravelElasticaBridge\Commands\StatusCommand;
use Limenet\LaravelElasticaBridge\Events\EventHandler;
use Limenet\LaravelElasticaBridge\Repository\IndexRepository;
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
            ->hasCommands([DeleteCommand::class, IndexCommand::class, StatusCommand::class]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ElasticaClient::class);
        $this->app->tag(config('elastica-bridge.indices'), 'elasticaBridgeIndices');

        $this->app->when(IndexRepository::class)
            ->needs('$indices')
            ->giveTagged('elasticaBridgeIndices');

        $this->app['events']->subscribe(EventHandler::class);
    }
}
