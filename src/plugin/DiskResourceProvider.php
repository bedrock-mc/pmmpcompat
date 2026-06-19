<?php

declare(strict_types=1);

namespace pocketmine\plugin;

class DiskResourceProvider implements ResourceProvider
{
    private string $file;

    public function __construct(string $path)
    {
        $this->file = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $path), '/') . '/';
    }

    public function getResource(string $filename)
    {
        $filename = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $filename), '/');
        $path = $this->file . $filename;
        if (!is_file($path)) {
            return null;
        }
        $resource = fopen($path, 'rb');
        return $resource === false ? null : $resource;
    }

    /** @return array<string, \SplFileInfo> */
    public function getResources(): array
    {
        $resources = [];
        if (!is_dir($this->file)) {
            return [];
        }
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->file)) as $resource) {
            if ($resource instanceof \SplFileInfo && $resource->isFile()) {
                $path = str_replace(DIRECTORY_SEPARATOR, '/', substr($resource->getPathname(), strlen($this->file)));
                $resources[$path] = $resource;
            }
        }
        return $resources;
    }
}
