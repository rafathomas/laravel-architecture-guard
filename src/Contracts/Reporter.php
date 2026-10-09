<?php

declare(strict_types=1);

namespace ArchitectureGuard\Contracts;

use ArchitectureGuard\Core\AnalysisResult;

interface Reporter
{
    public function report(AnalysisResult $result): string;
}
