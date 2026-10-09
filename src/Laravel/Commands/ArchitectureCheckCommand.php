<?php

declare(strict_types=1);

namespace ArchitectureGuard\Laravel\Commands;

use ArchitectureGuard\Core\ArchitectureEngine;
use ArchitectureGuard\Reports\ConsoleReporter;
use ArchitectureGuard\Reports\JsonReporter;
use Illuminate\Console\Command;

final class ArchitectureCheckCommand extends Command
{
    protected $signature = 'architecture:check {--format=console : console or json} {--path=* : Paths to inspect} {--rule=* : Restrict rules} {--no-progress : Kept for CI compatibility}';

    protected $description = 'Analyze configured source paths for architecture violations.';

    public function handle(ArchitectureEngine $engine): int
    {
        $format = (string) $this->option('format');
        if (! in_array($format, ['console', 'json'], true)) {
            $this->error('Format must be console or json.');

            return 2;
        } $paths = $this->option('path');
        $result = $engine->analyzeOnly($paths !== [] ? $paths : config('architecture-guard.paths', []), $this->option('rule'));
        $reporter = $format === 'json' ? new JsonReporter : new ConsoleReporter;
        $this->output->write($reporter->report($result));

        return $result->passed() ? self::SUCCESS : 1;
    }
}
