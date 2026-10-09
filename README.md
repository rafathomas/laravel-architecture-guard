# Laravel Architecture Guard

An AST-based architecture test suite for Laravel and PHP projects. It finds effective static dependencies without executing application code, then applies configurable boundaries.

## Requirements

PHP 8.3+, Composer 2, and Laravel 12 or 13 for Artisan integration. The analysis core uses `nikic/php-parser` and does not require a running Laravel application.

## Installation and quick start

```bash
composer require --dev vendor/laravel-architecture-guard
php artisan architecture:init
php artisan architecture:check
```

The provider is package-discovered. The init command publishes `config/architecture-guard.php` and will not overwrite it unless `--force` is supplied.

```php
'forbidden_dependencies' => [
    'App\\Services\\' => ['App\\Http\\Controllers\\'],
],
'layers' => [
    'http' => ['App\\Http\\'],
    'application' => ['App\\Application\\'],
    'domain' => ['App\\Domain\\'],
],
'allowed_layer_dependencies' => [
    'http' => ['application'],
    'application' => ['domain'],
],
```

For example, `App\Services\OrderService` instantiating `App\Http\Controllers\OrderController` produces a `forbidden_dependencies` error with source file and line. Run JSON mode in CI with `php artisan architecture:check --format=json`. Scope analysis with repeatable `--path` and `--rule` options.

## Included rules

- `forbidden_dependencies`: namespace boundary checks.
- `layer_dependencies`: directed layer allow-list checks.
- `circular_dependencies`: static class graph cycles.
- `namespace_conventions`: configured directory-to-namespace mappings.
- `controller_responsibility`: configured forbidden dependencies in controller namespaces.

## Custom rules

Implement `ArchitectureGuard\Contracts\ArchitectureRule`, register it in the container's `RuleRegistry`, and enable its identifier under `rules`. See [custom rules](docs/custom-rules.md).

## Limitations

Dynamic class names, reflection, service-container strings, generated code, and runtime conditionals cannot be proved from static AST alone. Results report statically observable references, not runtime certainty.

## Development

```bash
composer test
composer analyse
composer format:test
composer check
```

See [architecture](docs/architecture.md), [configuration](docs/configuration.md), [contributing](CONTRIBUTING.md), and [security](SECURITY.md). Licensed under MIT.
