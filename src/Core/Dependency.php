<?php

declare(strict_types=1);

namespace ArchitectureGuard\Core;

final readonly class Dependency
{
    public function __construct(public string $className, public string $type, public int $line) {}
}
