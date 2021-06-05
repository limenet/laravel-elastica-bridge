<?php

namespace Limenet\LaravelElasticaBridge\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Limenet\LaravelElasticaBridge\LaravelElasticaBridgeServiceProvider;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\CustomerIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\OrderIndex;
use Limenet\LaravelElasticaBridge\Tests\App\Elasticsearch\ProductIndex;
use Limenet\LaravelElasticaBridge\Tests\Database\Seeders\DatabaseSeeder;
use Orchestra\Testbench\TestCase as Orchestra;
use SetupTables;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Limenet\\LaravelElasticaBridge\\Tests\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }
    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']->set('elastica-bridge.indices', [CustomerIndex::class, OrderIndex::class, ProductIndex::class]);
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

        config()->set('elastica-bridge.elasticseach.host', env('ELASTICSEARCH_HOST', 'localhost'));
        config()->set('elastica-bridge.elasticseach.port', 9200);
    }

    protected function defineDatabaseMigrations():void
    {
        include_once __DIR__.'/database/migrations/SetupTables.php';
        (new SetupTables())->up();
        $this->artisan('db:seed', ['--class' => DatabaseSeeder::class])->run();
    }
}
