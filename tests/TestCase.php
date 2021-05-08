<?php

namespace Limenet\LaravelElasticaBridge\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Limenet\LaravelElasticaBridge\LaravelElasticaBridgeServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Spatie\\LaravelElasticaBridge\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelElasticaBridgeServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        include_once __DIR__.'/../database/migrations/create_laravel-elastica-bridge_table.php.stub';
        (new \CreatePackageTable())->up();
        */
    }
}
