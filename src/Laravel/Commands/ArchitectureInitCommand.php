<?php

declare(strict_types=1);

namespace ArchitectureGuard\Laravel\Commands;

use Illuminate\Console\Command;

final class ArchitectureInitCommand extends Command
{
    protected $signature = 'architecture:init {--force : Overwrite an existing configuration file}';

    protected $description = 'Publish the Architecture Guard configuration.';

    public function handle(): int
    {
        $destination = config_path('architecture-guard.php');
        if (file_exists($destination) && ! $this->option('force')) {
            $this->error('Configuration already exists. Use --force to overwrite it.');

            return 2;
        } $this->call('vendor:publish', ['--tag' => 'architecture-guard-config', '--force' => (bool) $this->option('force')]);
        $this->info('Architecture Guard configuration published. Run php artisan architecture:check.');

        return self::SUCCESS;
    }
}
