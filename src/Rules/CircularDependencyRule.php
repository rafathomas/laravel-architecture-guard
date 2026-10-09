<?php

declare(strict_types=1);

namespace ArchitectureGuard\Rules;

use ArchitectureGuard\Contracts\ArchitectureRule;
use ArchitectureGuard\Core\RuleContext;
use ArchitectureGuard\Core\Violation;

final class CircularDependencyRule implements ArchitectureRule
{
    public function identifier(): string
    {
        return 'circular_dependencies';
    }

    public function description(): string
    {
        return 'Detects static dependency cycles.';
    }

    public function analyze(RuleContext $context): array
    {
        $items = [];
        foreach ($context->graph->cycles() as $cycle) {
            $class = $cycle[0];
            $matches = array_values(array_filter($context->structures, static fn ($source) => $source->name === $class));
            $source = $matches[0] ?? null;
            $items[] = new Violation($this->identifier(), 'Circular dependency: '.implode(' -> ', $cycle).'.', 'error', $class, $source?->file, $source?->line, 'Static dependency cycle detected.', 'Invert one edge using an interface or application boundary.');
        }

        return $items;
    }
}
