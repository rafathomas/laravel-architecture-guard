<?php

declare(strict_types=1);

namespace ArchitectureGuard\Reports;

use ArchitectureGuard\Contracts\Reporter;
use ArchitectureGuard\Core\AnalysisResult;

final class ConsoleReporter implements Reporter
{
    public function report(AnalysisResult $result): string
    {
        $lines = ['Laravel Architecture Guard', ''];
        foreach ($result->violations as $v) {
            $lines[] = '['.strtoupper($v->severity).'] '.strtoupper($v->rule);
            $lines[] = $v->message;
            if ($v->file !== null) {
                $lines[] = 'File: '.$v->file.($v->line !== null ? ':'.$v->line : '');
            } if ($v->suggestion !== null) {
                $lines[] = 'Suggestion: '.$v->suggestion;
            } $lines[] = '';
        } $lines[] = "Files analyzed: {$result->filesAnalyzed}";
        $lines[] = 'Violations found: '.count($result->violations);
        $lines[] = $result->passed() ? 'Architecture check passed.' : 'Architecture check failed.';

        return implode(PHP_EOL, $lines).PHP_EOL;
    }
}
