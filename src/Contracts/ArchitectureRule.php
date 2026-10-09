<?php

declare(strict_types=1);

namespace ArchitectureGuard\Contracts;

use ArchitectureGuard\Core\RuleContext;
use ArchitectureGuard\Core\Violation;

interface ArchitectureRule
{
    public function identifier(): string;

    public function description(): string;

    /** @return list<Violation> */
    public function analyze(RuleContext $context): array;
}
