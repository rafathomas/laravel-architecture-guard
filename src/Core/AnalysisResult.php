<?php

declare(strict_types=1);

namespace ArchitectureGuard\Core;

final readonly class AnalysisResult
{
    /**
     * @param  list<SourceStructure>  $structures
     * @param  list<Violation>  $violations
     */
    public function __construct(public array $structures, public array $violations, public int $filesAnalyzed, public float $durationSeconds) {}

    public function passed(): bool
    {
        return $this->violations === [];
    }
}
