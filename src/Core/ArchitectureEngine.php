<?php

declare(strict_types=1);

namespace ArchitectureGuard\Core;

use ArchitectureGuard\Analysis\AstDependencyExtractor;
use ArchitectureGuard\Analysis\DependencyGraph;
use ArchitectureGuard\Analysis\PhpFileScanner;
use ArchitectureGuard\Contracts\Analyzer;
use ArchitectureGuard\Exceptions\AnalysisException;

final readonly class ArchitectureEngine implements Analyzer
{
    /** @param array<string, mixed> $configuration */
    public function __construct(private PhpFileScanner $scanner, private AstDependencyExtractor $extractor, private RuleRegistry $rules, private array $configuration) {}

    /** @param list<string> $paths */
    public function analyze(array $paths): AnalysisResult
    {
        return $this->run($paths, $this->configuration);
    }

    /**
     * @param  list<string>  $paths
     * @param  list<string>  $ruleIdentifiers
     */
    public function analyzeOnly(array $paths, array $ruleIdentifiers): AnalysisResult
    {
        $configuration = $this->configuration;
        if ($ruleIdentifiers !== []) {
            $configuration['rules'] = array_fill_keys($ruleIdentifiers, true);
        }

        return $this->run($paths, $configuration);
    }

    /**
     * @param  list<string>  $paths
     * @param  array<string, mixed>  $configuration
     */
    private function run(array $paths, array $configuration): AnalysisResult
    {
        $started = microtime(true);
        $structures = [];
        $files = $this->scanner->scan($paths, $configuration['exclude'] ?? []);
        foreach ($files as $file) {
            try {
                array_push($structures, ...$this->extractor->extract($file));
            } catch (AnalysisException $exception) {
                throw $exception;
            }
        }
        $graph = new DependencyGraph($structures);
        $context = new RuleContext($structures, $graph, $configuration);
        $violations = [];
        foreach ($this->rules->enabled($configuration['rules'] ?? []) as $rule) {
            array_push($violations, ...$rule->analyze($context));
        }
        usort($violations, static fn (Violation $a, Violation $b): int => [$a->rule, $a->file, $a->line] <=> [$b->rule, $b->file, $b->line]);

        return new AnalysisResult($structures, $violations, count($files), microtime(true) - $started);
    }
}
