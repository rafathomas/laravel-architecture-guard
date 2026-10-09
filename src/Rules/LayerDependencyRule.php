<?php

declare(strict_types=1);

namespace ArchitectureGuard\Rules;

use ArchitectureGuard\Contracts\ArchitectureRule;
use ArchitectureGuard\Core\RuleContext;
use ArchitectureGuard\Core\Violation;

final class LayerDependencyRule implements ArchitectureRule
{
    public function identifier(): string
    {
        return 'layer_dependencies';
    }

    public function description(): string
    {
        return 'Enforces configured layer dependency permissions.';
    }

    public function analyze(RuleContext $context): array
    {
        $violations = [];
        foreach ($context->structures as $source) {
            $from = $this->layer($source->name, $context->configuration['layers'] ?? []);
            if ($from === null) {
                continue;
            } foreach ($source->dependencies as $dependency) {
                $to = $this->layer($dependency->className, $context->configuration['layers'] ?? []);
                if ($to !== null && $from !== $to && ! in_array($to, $context->configuration['allowed_layer_dependencies'][$from] ?? [], true)) {
                    $violations[] = new Violation($this->identifier(), "Layer '{$from}' must not depend on layer '{$to}'.", 'error', $source->name, $source->file, $dependency->line, 'The dependency is outside the layer allow-list.', "Allow '{$to}' for '{$from}', or move the dependency.");
                }
            }
        }

return $violations;
    }

    /** @param array<string, list<string>> $layers */
    private function layer(string $class, array $layers): ?string
    {
        foreach ($layers as $layer => $prefixes) {
            foreach ($prefixes as $prefix) {
                if (str_starts_with($class, rtrim($prefix, '\\').'\\')) {
                    return $layer;
                }
            }
        }

return null;
    }
}
