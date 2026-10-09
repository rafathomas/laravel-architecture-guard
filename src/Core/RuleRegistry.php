<?php

declare(strict_types=1);

namespace ArchitectureGuard\Core;

use ArchitectureGuard\Contracts\ArchitectureRule;

final class RuleRegistry
{
    /** @var array<string, ArchitectureRule> */
    private array $rules = [];

    /** @param iterable<ArchitectureRule> $rules */
    public function __construct(iterable $rules = [])
    {
        foreach ($rules as $rule) {
            $this->register($rule);
        }
    }

    public function register(ArchitectureRule $rule): void
    {
        $this->rules[$rule->identifier()] = $rule;
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return list<ArchitectureRule>
     */
    public function enabled(array $settings): array
    {
        return array_values(array_filter($this->rules, static fn (ArchitectureRule $rule): bool => ($settings[$rule->identifier()] ?? false) === true));
    }
}
