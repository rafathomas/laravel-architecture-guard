<?php

declare(strict_types=1);

return [
    'paths' => [app_path()],
    'exclude' => ['vendor', 'node_modules', 'storage', 'bootstrap/cache'],
    'rules' => ['forbidden_dependencies' => true, 'layer_dependencies' => true, 'circular_dependencies' => true, 'namespace_conventions' => false, 'controller_responsibility' => false],
    'forbidden_dependencies' => ['App\\Services\\' => ['App\\Http\\Controllers\\']],
    'layers' => [], 'allowed_layer_dependencies' => [], 'namespace_conventions' => [],
    'controller_responsibility' => ['controller_namespaces' => ['App\\Http\\Controllers\\'], 'forbidden_dependencies' => []],
    'reporting' => ['format' => 'console', 'severity' => 'error'],
];
