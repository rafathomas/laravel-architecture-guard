<?php

declare(strict_types=1);

it('registers the check command and emits valid JSON', function (): void {
    $this->artisan('architecture:check', ['--path' => [base_path('tests/Fixtures')], '--format' => 'json'])
        ->assertExitCode(0);
});
