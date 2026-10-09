<?php

declare(strict_types=1);

namespace ArchitectureGuard\Core;

final readonly class SourceStructure
{
    /** @param list<Dependency> $dependencies */
    public function __construct(
        public string $name,
        public string $namespace,
        public string $file,
        public int $line,
        public string $kind,
        public array $dependencies,
    ) {}
}
