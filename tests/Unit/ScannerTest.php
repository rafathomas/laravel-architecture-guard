<?php

declare(strict_types=1);

use ArchitectureGuard\Analysis\PhpFileScanner;

it('scans PHP files once and excludes configured folders', function (): void {
    $root = sys_get_temp_dir().'/guard-scan-'.uniqid();
    mkdir($root);
    mkdir($root.'/vendor');
    file_put_contents($root.'/A.php', '<?php');
    file_put_contents($root.'/vendor/B.php', '<?php');
    file_put_contents($root.'/readme.txt', 'x');
    expect((new PhpFileScanner)->scan([$root], ['vendor']))->toHaveCount(1);
    unlink($root.'/A.php');
    unlink($root.'/vendor/B.php');
    unlink($root.'/readme.txt');
    rmdir($root.'/vendor');
    rmdir($root);
});
