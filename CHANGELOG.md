# Changelog

All notable changes to `laravel-elastica-bridge` will be documented in this file.

## v1.4.2 - 2022-10-16

- When blue/green indices are enabled and an index with the base name (i.e. without the blue/green suffix) exists, that index is deleted automatically to ensure indexing works as expected

## v1.4.0 - 2022-06-24

- Update underlying skeleton (#3)
- Indexing lock (#4)

## v1.3.0 - 2022-02-26

- Added support for Laravel 9 (#2)

## v1.2.0 - 2021-06-05

- Listen to model events and propagate changes to Elasticsearch (#1)
- More tests

## v1.1.0 - 2021-05-16

- Provide default implementation for `Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface::toElasticsearch()` in `ElasticsearchableTrait`
- Provide default implementation for `Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface::shouldIndex()` in `ElasticsearchableTrait`
- Remove unused method `Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface::getModel()`
- Added tests
- Rename `elasticsearch:index` to  `elastica-bridge:index` for consistency with package name and  `elastica-bridge:status`

## v1.0.0 - 2021-05-09

- Initial release
