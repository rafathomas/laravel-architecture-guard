<?php

declare(strict_types=1);

namespace ArchitectureGuard\Reports;

use ArchitectureGuard\Contracts\Reporter;
use ArchitectureGuard\Core\AnalysisResult;

final class JsonReporter implements Reporter
{
    public function report(AnalysisResult $result): string
    {
        return json_encode(['status' => $result->passed() ? 'passed' : 'failed', 'files_analyzed' => $result->filesAnalyzed, 'duration_seconds' => $result->durationSeconds, 'violations' => $result->violations], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;
    }
}
