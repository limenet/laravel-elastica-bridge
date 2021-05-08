#

[![Latest Version on Packagist](https://img.shields.io/packagist/v/limenet/laravel-elastica-bridge.svg?style=flat-square)](https://packagist.org/packages/limenet/laravel-elastica-bridge)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/limenet/laravel-elastica-bridge/run-tests?label=tests)](https://github.com/limenet/laravel-elastica-bridge/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/limenet/laravel-elastica-bridge/Check%20&%20fix%20styling?label=code%20style)](https://github.com/limenet/laravel-elastica-bridge/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/limenet/laravel-elastica-bridge.svg?style=flat-square)](https://packagist.org/packages/limenet/laravel-elastica-bridge)

A simple bridge between Laravel and Elasticsearch using Elastica, based on [https://github.com/valantic/pimcore-elastica-bridge](https://github.com/valantic/pimcore-elastica-bridge).


## Installation

You can install the package via composer:

```bash
composer require limenet/laravel-elastica-bridge
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Limenet\LaravelElasticaBridge\LaravelElasticaBridgeServiceProvider" --tag="laravel-elastica-bridge-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Limenet\LaravelElasticaBridge\LaravelElasticaBridgeServiceProvider" --tag="laravel-elastica-bridge-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$laravel-elastica-bridge = new Limenet\LaravelElasticaBridge();
echo $laravel-elastica-bridge->echoPhrase('Hello, Spatie!');
```

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
