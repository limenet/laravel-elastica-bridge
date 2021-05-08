<?php

namespace Limenet\LaravelElasticaBridge;

use Limenet\LaravelElasticaBridge\Commands\LaravelElasticaBridgeCommand;
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
            ->hasViews()
            ->hasMigration('create_laravel-elastica-bridge_table')
            ->hasCommand(LaravelElasticaBridgeCommand::class);
    }
}
