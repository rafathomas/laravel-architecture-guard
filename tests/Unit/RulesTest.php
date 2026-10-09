<?php

declare(strict_types=1);

use ArchitectureGuard\Analysis\DependencyGraph;
use ArchitectureGuard\Core\Dependency;
use ArchitectureGuard\Core\RuleContext;
use ArchitectureGuard\Core\SourceStructure;
use ArchitectureGuard\Rules\CircularDependencyRule;
use ArchitectureGuard\Rules\ForbiddenDependencyRule;
use ArchitectureGuard\Rules\LayerDependencyRule;

it('reports forbidden namespace dependencies with prefix boundaries', function (): void {
    $source = new SourceStructure('App\\Services\\OrderService', 'App\\Services', '/tmp/Order.php', 1, 'class', [new Dependency('App\\Http\\Controllers\\OrderController', 'instantiation', 9)]);
    $context = new RuleContext([$source], new DependencyGraph([$source]), ['forbidden_dependencies' => ['App\\Services\\' => ['App\\Http\\Controllers\\']]]);
    expect((new ForbiddenDependencyRule)->analyze($context))->toHaveCount(1);
});

it('detects one dependency cycle', function (): void {
    $a = new SourceStructure('App\\A', 'App', '/tmp/A.php', 1, 'class', [new Dependency('App\\B', 'type', 3)]);
    $b = new SourceStructure('App\\B', 'App', '/tmp/B.php', 1, 'class', [new Dependency('App\\A', 'type', 3)]);
    $context = new RuleContext([$a, $b], new DependencyGraph([$a, $b]), []);
    expect((new CircularDependencyRule)->analyze($context))->toHaveCount(1);
});

it('disallows unlisted layer dependencies', function (): void {
    $source = new SourceStructure('App\\Http\\OrderController', 'App\\Http', '/tmp/C.php', 1, 'class', [new Dependency('App\\Infrastructure\\Db', 'type', 7)]);
    $context = new RuleContext([$source], new DependencyGraph([$source]), ['layers' => ['http' => ['App\\Http\\'], 'infrastructure' => ['App\\Infrastructure\\']], 'allowed_layer_dependencies' => ['http' => []]]);
    expect((new LayerDependencyRule)->analyze($context))->toHaveCount(1);
});
