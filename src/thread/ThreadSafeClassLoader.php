<?php

declare(strict_types=1);

namespace pocketmine\thread;

class ThreadSafeClassLoader extends \pmmp\thread\ThreadSafe
{
    private const ENV_PATHS = 'PMMPCOMPAT_CLASSLOAD_PATHS';

    private static ?self $default = null;

    private \pmmp\thread\ThreadSafeArray $paths;
    private bool $registered = false;

    public function __construct()
    {
        $this->paths = new \pmmp\thread\ThreadSafeArray();
    }

    public static function getDefault(): self
    {
        return self::$default ??= new self();
    }

    public function addPath(string $namespacePrefix, string $path, bool $prepend = false): void
    {
        $prefix = trim($namespacePrefix, '\\');
        if (!isset($this->paths[$prefix])) {
            $this->paths[$prefix] = new \pmmp\thread\ThreadSafeArray();
        }
        $paths = $this->paths[$prefix];
        if ($prepend) {
            $current = method_exists($paths, 'toArray') ? $paths->toArray() : iterator_to_array($paths);
            array_unshift($current, rtrim($path, '/\\'));
            $this->paths[$prefix] = \pmmp\thread\ThreadSafeArray::fromArray($current);
        } else {
            $paths[] = rtrim($path, '/\\');
        }
        self::writeEnvironmentPaths(self::snapshotPaths());
    }

    public static function loadEnvironmentPaths(): void
    {
        $raw = getenv(self::ENV_PATHS);
        if ($raw === false || $raw === '') {
            return;
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return;
        }
        $loader = self::getDefault();
        foreach ($decoded as $prefix => $paths) {
            if (!is_string($prefix) || !is_array($paths)) {
                continue;
            }
            foreach ($paths as $path) {
                if (is_string($path)) {
                    $loader->addPath($prefix, $path);
                }
            }
        }
    }

    /** @return array<string, list<string>> */
    private static function snapshotPaths(): array
    {
        $loader = self::getDefault();
        $out = [];
        foreach ($loader->paths as $prefix => $paths) {
            $out[$prefix] = method_exists($paths, 'toArray') ? array_values($paths->toArray()) : array_values(iterator_to_array($paths));
        }
        return $out;
    }

    /** @param array<string, list<string>> $paths */
    private static function writeEnvironmentPaths(array $paths): void
    {
        $encoded = json_encode($paths);
        if (is_string($encoded)) {
            putenv(self::ENV_PATHS . '=' . $encoded);
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
