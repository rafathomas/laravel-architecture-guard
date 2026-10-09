<?php

declare(strict_types=1);

namespace ArchitectureGuard\Rules;

use ArchitectureGuard\Contracts\ArchitectureRule;
use ArchitectureGuard\Core\RuleContext;
use ArchitectureGuard\Core\Violation;

final class ControllerResponsibilityRule implements ArchitectureRule
{
    public function identifier(): string
    {
        return 'controller_responsibility';
    }

    public function description(): string
    {
        return 'Prevents controllers from using configured forbidden namespaces.';
    }

    public function analyze(RuleContext $context): array
    {
        $out = [];
        $settings = $context->configuration['controller_responsibility'] ?? [];
        foreach ($context->structures as $source) {
            if (! $this->matchesAny($source->name, $settings['controller_namespaces'] ?? ['App\\Http\\Controllers\\'])) {
                continue;
            } foreach ($source->dependencies as $dependency) {
                if ($this->matchesAny($dependency->className, $settings['forbidden_dependencies'] ?? [])) {
                    $out[] = new Violation($this->identifier(), "Controller {$source->name} uses forbidden {$dependency->className}.", 'error', $source->name, $source->file, $dependency->line, 'A configured controller boundary was crossed.', 'Delegate this work to an allowed application service.');
                }
            }
        }

return $out;
    }

    /** @param list<string> $prefixes */
    private function matchesAny(string $name, array $prefixes): bool
    {
        foreach ($prefixes as $prefix) {
            if (str_starts_with($name, rtrim($prefix, '\\').'\\')) {
                return true;
            }
        }

return false;
    }
}
