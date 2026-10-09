<?php

declare(strict_types=1);

namespace ArchitectureGuard\Core;

use ArchitectureGuard\Analysis\DependencyGraph;

final readonly class RuleContext
{
    /**
     * @param  list<SourceStructure>  $structures
     * @param  array<string, mixed>  $configuration
     */
    public function __construct(public array $structures, public DependencyGraph $graph, public array $configuration) {}
}
