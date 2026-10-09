<?php

declare(strict_types=1);

namespace ArchitectureGuard\Laravel;

use ArchitectureGuard\Analysis\AstDependencyExtractor;
use ArchitectureGuard\Analysis\PhpFileScanner;
use ArchitectureGuard\Core\ArchitectureEngine;
use ArchitectureGuard\Core\RuleRegistry;
use ArchitectureGuard\Laravel\Commands\ArchitectureCheckCommand;
use ArchitectureGuard\Laravel\Commands\ArchitectureInitCommand;
use ArchitectureGuard\Rules\CircularDependencyRule;
use ArchitectureGuard\Rules\ControllerResponsibilityRule;
use ArchitectureGuard\Rules\ForbiddenDependencyRule;
use ArchitectureGuard\Rules\LayerDependencyRule;
use ArchitectureGuard\Rules\NamespaceConventionRule;
use Illuminate\Support\ServiceProvider;

final class ArchitectureGuardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/architecture-guard.php', 'architecture-guard');
        $this->app->singleton(RuleRegistry::class, static fn () => new RuleRegistry([new ForbiddenDependencyRule, new LayerDependencyRule, new CircularDependencyRule, new NamespaceConventionRule, new ControllerResponsibilityRule]));
        $this->app->singleton(ArchitectureEngine::class, fn ($app) => new ArchitectureEngine(new PhpFileScanner, new AstDependencyExtractor, $app->make(RuleRegistry::class), $app['config']->get('architecture-guard')));
    }

    public function boot(): void
    {
        $this->publishes([__DIR__.'/../../config/architecture-guard.php' => config_path('architecture-guard.php')], 'architecture-guard-config');
        if ($this->app->runningInConsole()) {
            $this->commands([ArchitectureCheckCommand::class, ArchitectureInitCommand::class]);
        }
    }
}
