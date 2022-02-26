# Changelog

All notable changes to `laravel-elastica-bridge` will be documented in this file.

## 1.3.0 - 2022-02-26

- Added support for Laravel 9 (#2)

## 1.2.0 - 2021-06-05

- Listen to model events and propagate changes to Elasticsearch (#1)
- More tests

## 1.1.0 - 2021-05-16

- Provide default implementation for `Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface::toElasticsearch()` in `ElasticsearchableTrait`
- Provide default implementation for `Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface::shouldIndex()` in `ElasticsearchableTrait`
- Remove unused method `Limenet\LaravelElasticaBridge\Model\ElasticsearchableInterface::getModel()`
- Added tests
- Rename `elasticsearch:index` to  `elastica-bridge:index` for consistency with package name and  `elastica-bridge:status`

## 1.0.0 - 2021-05-09

- Initial release
