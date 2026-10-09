<?php

declare(strict_types=1);

use ArchitectureGuard\Analysis\AstDependencyExtractor;

it('extracts effective dependencies and resolves aliases without treating imports as dependencies', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'guard-');
    file_put_contents($file, <<<'PHP'
<?php
namespace App\Services;
use Vendor\Package\Client as ApiClient;
use Vendor\Unused\Thing;
class PaymentService extends \BaseService { public function pay(ApiClient $client): void { new \Domain\Invoice(); } }
PHP);
    $structure = (new AstDependencyExtractor)->extract($file)[0];
    $names = array_map(static fn ($d): string => $d->className, $structure->dependencies);
    expect($structure->name)->toBe('App\\Services\\PaymentService')
        ->and($names)->toContain('Vendor\\Package\\Client', 'BaseService', 'Domain\\Invoice')
        ->and($names)->not->toContain('Vendor\\Unused\\Thing');
    unlink($file);
});
