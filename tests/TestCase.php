<?php

namespace Limenet\LaravelElasticaBridge\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Limenet\LaravelElasticaBridge\LaravelElasticaBridgeServiceProvider;
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

    protected function getPackageProviders($app)
    {
        return [
            LaravelElasticaBridgeServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }

    protected function defineDatabaseMigrations():void
    {
        include_once __DIR__.'/database/migrations/SetupTables.php';
        (new SetupTables())->up();
        $this->artisan('db:seed', ['--class' => DatabaseSeeder::class])->run();
    }
}
