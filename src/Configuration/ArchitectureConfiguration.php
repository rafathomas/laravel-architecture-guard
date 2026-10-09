<?php

declare(strict_types=1);

namespace ArchitectureGuard\Configuration;

use ArchitectureGuard\Exceptions\ConfigurationException;

final readonly class ArchitectureConfiguration
{
    /** @param array<string, mixed> $values */
    public function __construct(public array $values)
    {
        self::validate($values);
    }

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return ['paths' => ['app'], 'exclude' => ['vendor', 'node_modules', 'storage', 'bootstrap/cache'], 'rules' => ['forbidden_dependencies' => true, 'layer_dependencies' => true, 'circular_dependencies' => true, 'namespace_conventions' => false, 'controller_responsibility' => false], 'forbidden_dependencies' => [], 'layers' => [], 'allowed_layer_dependencies' => [], 'namespace_conventions' => [], 'controller_responsibility' => [], 'reporting' => ['format' => 'console', 'severity' => 'error']];
    }

    /** @param array<string, mixed> $values */
    private static function validate(array $values): void
    {
        foreach (['paths', 'exclude', 'rules'] as $key) {
            if (isset($values[$key]) && ! is_array($values[$key])) {
                throw new ConfigurationException("Configuration '{$key}' must be an array.");
            }
        } if (isset($values['reporting']['format']) && ! in_array($values['reporting']['format'], ['console', 'json'], true)) {
            throw new ConfigurationException('Reporting format must be console or json.');
        }
    }
}
