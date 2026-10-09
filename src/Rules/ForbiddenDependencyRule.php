<?php

declare(strict_types=1);

namespace ArchitectureGuard\Rules;

use ArchitectureGuard\Contracts\ArchitectureRule;
use ArchitectureGuard\Core\RuleContext;
use ArchitectureGuard\Core\Violation;

final class ForbiddenDependencyRule implements ArchitectureRule
{
    public function identifier(): string
    {
        return 'forbidden_dependencies';
    }

    public function description(): string
    {
        return 'Prevents configured namespace dependency directions.';
    }

    public function analyze(RuleContext $context): array
    {
        $violations = [];
        foreach ($context->structures as $source) {
            foreach ($source->dependencies as $dependency) {
                foreach (($context->configuration['forbidden_dependencies'] ?? []) as $from => $targets) {
                    if ($this->matches($source->name, $from)) {
                        foreach ($targets as $target) {
                            if ($this->matches($dependency->className, $target)) {
                                $violations[] = new Violation($this->identifier(), "{$source->name} depends on forbidden {$dependency->className}.", 'error', $source->name, $source->file, $dependency->line, 'This dependency violates a configured namespace boundary.', 'Move the dependency behind an allowed abstraction.');
                            }
                        }
                    }
                }
            }
        }

return $violations;
    }

    private function matches(string $class, string $prefix): bool
    {
        return rtrim($class, '\\').'\\' === rtrim($prefix, '\\').'\\' || str_starts_with($class, rtrim($prefix, '\\').'\\');
    }
}
