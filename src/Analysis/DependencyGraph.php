<?php

declare(strict_types=1);

namespace ArchitectureGuard\Analysis;

use ArchitectureGuard\Core\SourceStructure;

final class DependencyGraph
{
    /** @var array<string, list<string>> */
    private array $edges = [];

    /** @var array<string, list<string>> */
    private array $reverse = [];

    /** @param list<SourceStructure> $structures */
    public function __construct(array $structures)
    {
        foreach ($structures as $structure) {
            $this->edges[$structure->name] = array_values(array_unique(array_map(static fn ($d): string => $d->className, $structure->dependencies)));
            foreach ($this->edges[$structure->name] as $dependency) {
                $this->reverse[$dependency][] = $structure->name;
            }
        }
    }

    /** @return list<string> */
    public function dependenciesOf(string $class): array
    {
        return $this->edges[$class] ?? [];
    }

    /** @return list<string> */
    public function dependentsOf(string $class): array
    {
        return $this->reverse[$class] ?? [];
    }

    /** @return list<list<string>> */
    public function cycles(): array
    {
        $cycles = [];
        $state = [];
        $stack = [];
        $visit = function (string $node) use (&$visit, &$cycles, &$state, &$stack): void {
            $state[$node] = 1;
            $stack[] = $node;
            foreach ($this->dependenciesOf($node) as $next) {
                if (! isset($this->edges[$next])) {
                    continue;
                } if (($state[$next] ?? 0) === 1) {
                    $start = array_search($next, $stack, true);
                    if ($start !== false) {
                        $cycles[] = array_merge(array_slice($stack, $start), [$next]);
                    }
                } elseif (($state[$next] ?? 0) === 0) {
                    $visit($next);
                }
            } array_pop($stack);
            $state[$node] = 2;
        };
        foreach (array_keys($this->edges) as $node) {
            if (($state[$node] ?? 0) === 0) {
                $visit($node);
            }
        }

        return $this->uniqueCycles($cycles);
    }

    /**
     * @param  list<list<string>>  $cycles
     * @return list<list<string>>
     */
    private function uniqueCycles(array $cycles): array
    {
        $unique = [];
        foreach ($cycles as $cycle) {
            array_pop($cycle);
            $rotations = [];
            for ($i = 0; $i < count($cycle); $i++) {
                $r = array_merge(array_slice($cycle, $i), array_slice($cycle, 0, $i));
                $rotations[] = implode('|', $r);
            } $key = min($rotations);
            $unique[$key] = array_merge(explode('|', $key), [explode('|', $key)[0]]);
        }

        return array_values($unique);
    }
}
