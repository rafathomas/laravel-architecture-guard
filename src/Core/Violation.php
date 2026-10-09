<?php

declare(strict_types=1);

namespace ArchitectureGuard\Core;

final readonly class Violation implements \JsonSerializable
{
    public function __construct(
        public string $rule,
        public string $message,
        public string $severity = 'error',
        public ?string $className = null,
        public ?string $file = null,
        public ?int $line = null,
        public ?string $description = null,
        public ?string $suggestion = null,
    ) {}

    /** @return array<string, string|int|null> */
    public function jsonSerialize(): array
    {
        return ['rule' => $this->rule, 'severity' => $this->severity, 'message' => $this->message, 'class' => $this->className, 'file' => $this->file, 'line' => $this->line, 'description' => $this->description, 'suggestion' => $this->suggestion];
    }
}
