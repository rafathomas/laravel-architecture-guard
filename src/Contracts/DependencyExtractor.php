<?php

declare(strict_types=1);

namespace ArchitectureGuard\Contracts;

use ArchitectureGuard\Core\SourceStructure;

interface DependencyExtractor
{
    /** @return list<SourceStructure> */
    public function extract(string $file): array;
}
