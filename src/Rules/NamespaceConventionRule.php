<?php

declare(strict_types=1);

namespace ArchitectureGuard\Rules;

use ArchitectureGuard\Contracts\ArchitectureRule;
use ArchitectureGuard\Core\RuleContext;
use ArchitectureGuard\Core\Violation;

final class NamespaceConventionRule implements ArchitectureRule
{
    public function identifier(): string
    {
        return 'namespace_conventions';
    }

    public function description(): string
    {
        return 'Checks configured path-to-namespace conventions.';
    }

    public function analyze(RuleContext $context): array
    {
        $out = [];
        foreach ($context->structures as $structure) {
            foreach ($context->configuration['namespace_conventions'] ?? [] as $path => $namespace) {
                if ($this->containsPath($structure->file, $path) && ! str_starts_with($structure->name, rtrim($namespace, '\\').'\\')) {
                    $out[] = new Violation($this->identifier(), "{$structure->name} must use namespace {$namespace}.", 'error', $structure->name, $structure->file, $structure->line, 'The class namespace does not match its configured directory.', "Use namespace {$namespace} or update the convention.");
                }
            }
        }

return $out;
    }

    private function containsPath(string $file, string $path): bool
    {
        return str_contains(str_replace('\\', '/', $file), '/'.trim(str_replace('\\', '/', $path), '/').'/');
    }
}
