<?php

declare(strict_types=1);

namespace pocketmine\thread;

class ThreadSafeClassLoader
{
    /** @var array<string, list<string>> */
    private array $paths = [];
    private bool $registered = false;

    public function __construct()
    {
    }

    public function addPath(string $namespacePrefix, string $path, bool $prepend = false): void
    {
        $prefix = trim($namespacePrefix, '\\');
        $this->paths[$prefix] ??= [];
        if ($prepend) {
            array_unshift($this->paths[$prefix], rtrim($path, '/\\'));
        } else {
            $this->paths[$prefix][] = rtrim($path, '/\\');
        }
    }

    public function findClass(string $className): ?string
    {
        $className = ltrim($className, '\\');
        foreach ($this->paths as $prefix => $paths) {
            if ($prefix !== '' && $className !== $prefix && !str_starts_with($className, $prefix . '\\')) {
                continue;
            }
            $relative = $prefix === '' ? $className : substr($className, strlen($prefix) + 1);
            $relativePath = str_replace('\\', '/', $relative) . '.php';
            foreach ($paths as $path) {
                $file = $path . '/' . $relativePath;
                if (is_file($file)) {
                    return $file;
                }
            }
        }
        return null;
    }

    public function loadClass(string $className): bool
    {
        $file = $this->findClass($className);
        if ($file === null) {
            return false;
        }
        require_once $file;
        return true;
    }

    public function register(bool $prepend = false): bool
    {
        if (!$this->registered) {
            spl_autoload_register([$this, 'loadClass'], true, $prepend);
            $this->registered = true;
        }
        return true;
    }
}
