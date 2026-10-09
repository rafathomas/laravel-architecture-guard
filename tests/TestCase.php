<?php

declare(strict_types=1);

namespace ArchitectureGuard\Tests;

use ArchitectureGuard\Laravel\ArchitectureGuardServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [ArchitectureGuardServiceProvider::class];
    }
}
