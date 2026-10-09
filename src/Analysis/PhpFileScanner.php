<?php

declare(strict_types=1);

namespace ArchitectureGuard\Analysis;

final class PhpFileScanner
{
    /**
     * @param  list<string>  $paths
     * @param  list<string>  $excluded
     * @return list<string>
     */
    public function scan(array $paths, array $excluded = []): array
    {
        $files = [];
        $seen = [];
        foreach ($paths as $path) {
            $realPath = realpath($path);
            if ($realPath === false || $this->isExcluded($realPath, $excluded)) {
                continue;
            }
            if (is_file($realPath)) {
                $this->add($realPath, $files, $seen);

                continue;
            }
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($realPath, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::LEAVES_ONLY);
            foreach ($iterator as $item) {
                if ($item->isLink() || ! $item->isFile() || $item->getExtension() !== 'php' || $this->isExcluded($item->getPathname(), $excluded)) {
                    continue;
                }
                $this->add($item->getPathname(), $files, $seen);
            }
        }
        sort($files, SORT_STRING);

        return $files;
    }

    /**
     * @param  list<string>  $files
     * @param  array<string, true>  $seen
     */
    private function add(string $path, array &$files, array &$seen): void
    {
        $real = realpath($path);
        if ($real !== false && ! isset($seen[$real]) && is_readable($real)) {
            $seen[$real] = true;
            $files[] = $real;
        }
    }

    /** @param list<string> $excluded */
    private function isExcluded(string $path, array $excluded): bool
    {
        $segments = preg_split('#[\\\\/]#', str_replace('\\\\', '/', $path)) ?: [];

        return array_intersect($segments, $excluded) !== [];
    }
}
