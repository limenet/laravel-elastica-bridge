# Laravel-Elastica Bridge

[![Latest Version on Packagist](https://img.shields.io/packagist/v/limenet/laravel-elastica-bridge.svg?style=flat)](https://packagist.org/packages/limenet/laravel-elastica-bridge)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/limenet/laravel-elastica-bridge/run-tests.yml?label=tests)](https://github.com/limenet/laravel-elastica-bridge/actions/workflows/run-tests.yml)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/limenet/laravel-elastica-bridge/fix-php-code-style-issues.yml?label=code%20style)](https://github.com/limenet/laravel-elastica-bridge/actions/workflows/fix-php-code-style-issues.yml)
[![GitHub PHPStan Action Status](https://img.shields.io/github/actions/workflow/status/limenet/laravel-elastica-bridge/phpstan.yml?label=phpstan)](https://github.com/limenet/laravel-elastica-bridge/actions/workflows/phpstan.yml)
[![codecov](https://codecov.io/github/limenet/laravel-elastica-bridge/branch/main/graph/badge.svg?token=2ZE85IILKR)](https://codecov.io/github/limenet/laravel-elastica-bridge)
[![Total Downloads](https://img.shields.io/packagist/dt/limenet/laravel-elastica-bridge.svg?style=flat)](https://packagist.org/packages/limenet/laravel-elastica-bridge)
[![License](https://img.shields.io/github/license/limenet/laravel-elastica-bridge)](LICENSE.md)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/limenet/laravel-elastica-bridge)

A simple bridge between Laravel and Elasticsearch using Elastica, based on [https://github.com/valantic/pimcore-elastica-bridge](https://github.com/valantic/pimcore-elastica-bridge).


## Requirements

- PHP 8.1+
- Laravel 10.0
- [Job Batching](https://laravel.com/docs/8.x/queues#job-batching) is setup

## Installation

You can install the package via composer:

```bash
composer require limenet/laravel-elastica-bridge
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Limenet\LaravelElasticaBridge\LaravelElasticaBridgeServiceProvider" --tag="elastica-bridge-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Limenet\LaravelElasticaBridge\LaravelElasticaBridgeServiceProvider" --tag="elastica-bridge-config"
```

This is the contents of the published config file:

```php
return [
    'elasticsearch' => [
        'host' => env('ELASTICSEARCH_HOST', 'localhost'),
        'port' => env('ELASTICSEARCH_PORT', '9200'),
    ],
    'indices' => [],
    'events' => [
        'listen' => true,
    ],
];

```

## Usage

1. Add `Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface` and `Limenet\LaravelElasticaBridge\Model\ElasticsearchableTrait` to a model
2. Create a class extending `Limenet\LaravelElasticaBridge\Index\AbstractIndex`
3. Add the index to your config (`elastica-bridge.indices`)
4. Run `php artisan elastica-bridge:index`
5. Check using `php artisan elastica-bridge:status`

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Linus Metzler](https://github.com/limenet)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
