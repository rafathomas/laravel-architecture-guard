<?php

declare(strict_types=1);

namespace ArchitectureGuard\Contracts;

use ArchitectureGuard\Core\AnalysisResult;

interface Analyzer
{
    /** @param list<string> $paths */
    public function analyze(array $paths): AnalysisResult;
}
