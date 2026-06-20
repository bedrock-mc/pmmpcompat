<?php

declare(strict_types=1);

namespace pocketmine\utils;

final class Filesystem
{
    /** @var array<string, resource> */
    private static array $lockFileHandles = [];
    /** @var array<string, string> */
    private static array $cleanedPaths = [];

    public const CLEAN_PATH_SRC_PREFIX = 'pmsrc';
    public const CLEAN_PATH_PLUGINS_PREFIX = 'plugins';

    private function __construct() {}

    private static function join(string ...$parts): string
    {
        $absolute = isset($parts[0][0]) && ($parts[0][0] === '/' || $parts[0][0] === '\\');
        $joined = preg_replace('#/+#', '/', implode('/', array_map(static fn(string $p): string => trim($p, '/\\'), $parts))) ?? implode(DIRECTORY_SEPARATOR, $parts);
        return $absolute ? '/' . ltrim($joined, '/') : $joined;
    }

    public static function recursiveUnlink(string $dir): void
    {
        if (is_dir($dir)) {
            foreach (scandir($dir, SCANDIR_SORT_NONE) ?: [] as $object) {
                if ($object === '.' || $object === '..') {
                    continue;
                }
                self::recursiveUnlink(self::join($dir, $object));
            }
            @rmdir($dir);
        } elseif (is_file($dir) || is_link($dir)) {
            @unlink($dir);
        }
    }

    public static function recursiveCopy(string $origin, string $destination): void
    {
        if (!is_dir($origin)) {
            throw new \RuntimeException($origin . ' does not exist, or is not a directory');
        }
        if (!is_dir($destination)) {
            if (file_exists($destination)) {
                throw new \RuntimeException($destination . ' already exists, and is not a directory');
            }
            if (!is_dir(dirname($destination))) {
                throw new \RuntimeException('The parent directory of ' . $destination . ' does not exist, or is not a directory');
            }
            mkdir($destination, 0777, true);
        }
        foreach (scandir($origin, SCANDIR_SORT_NONE) ?: [] as $object) {
            if ($object === '.' || $object === '..') {
                continue;
            }
            $from = self::join($origin, $object);
            $to = self::join($destination, $object);
            if (is_dir($from)) {
                self::recursiveCopy($from, $to);
            } else {
                if (!copy($from, $to)) {
                    throw new \RuntimeException('Failed to copy ' . $from . ' to ' . $to);
                }
            }
        }
    }

    public static function addCleanedPath(string $path, string $replacement): void
    {
        self::$cleanedPaths[$path] = $replacement;
        uksort(self::$cleanedPaths, static fn(string $a, string $b): int => strlen($b) <=> strlen($a));
    }

    /** @return array<string, string> */
    public static function getCleanedPaths(): array
    {
        return self::$cleanedPaths;
    }

    public static function cleanPath(string $path): string
    {
        $result = str_replace([DIRECTORY_SEPARATOR, '.php', 'phar://'], ['/', '', ''], $path);
        foreach (Utils::stringifyKeys(self::$cleanedPaths) as $cleanPath => $replacement) {
            $cleanPath = rtrim(str_replace([DIRECTORY_SEPARATOR, 'phar://'], ['/', ''], $cleanPath), '/');
            if ($cleanPath !== '' && str_starts_with($result, $cleanPath)) {
                $result = ltrim(str_replace($cleanPath, $replacement, $result), '/');
            }
        }
        return $result;
    }

    public static function createLockFile(string $lockFilePath): ?int
    {
        $resource = fopen($lockFilePath, 'a+b');
        if ($resource === false) {
            throw new \InvalidArgumentException('Failed to open lock file');
        }
        if (!flock($resource, LOCK_EX | LOCK_NB)) {
            flock($resource, LOCK_SH);
            rewind($resource);
            $pid = stream_get_contents($resource);
            fclose($resource);
            return is_string($pid) && preg_match('/^\d+$/', trim($pid)) === 1 ? (int) trim($pid) : -1;
        }
        ftruncate($resource, 0);
        fwrite($resource, (string) getmypid());
        fflush($resource);
        flock($resource, LOCK_SH);
        $real = realpath($lockFilePath) ?: $lockFilePath;
        self::$lockFileHandles[$real] = $resource;
        return null;
    }

    public static function releaseLockFile(string $lockFilePath): void
    {
        $real = realpath($lockFilePath) ?: $lockFilePath;
        if (isset(self::$lockFileHandles[$real])) {
            flock(self::$lockFileHandles[$real], LOCK_UN);
            fclose(self::$lockFileHandles[$real]);
            unset(self::$lockFileHandles[$real]);
            @unlink($real);
        }
    }

    public static function safeFilePutContents(string $fileName, string $contents, int $flags = 0, $context = null): void
    {
        $directory = dirname($fileName);
        if (!is_dir($directory)) {
            throw new \RuntimeException('Target directory path does not exist or is not a directory');
        }
        if (is_dir($fileName)) {
            throw new \RuntimeException('Target file path already exists and is not a file');
        }
        $tmp = tempnam($directory, basename($fileName) . '.tmp.');
        if ($tmp === false) {
            throw new \RuntimeException('Failed to create temporary file');
        }
        $written = $context !== null ? file_put_contents($tmp, $contents, $flags, $context) : file_put_contents($tmp, $contents, $flags);
        if ($written === false) {
            @unlink($tmp);
            throw new \RuntimeException('Failed to write temporary file');
        }
        if (!@rename($tmp, $fileName)) {
            if (!@copy($tmp, $fileName)) {
                @unlink($tmp);
                throw new \RuntimeException('Failed to move temporary file contents into target file');
            }
            @unlink($tmp);
        }
    }

    public static function fileGetContents(string $fileName, bool $useIncludePath = false, $context = null, int $offset = 0, ?int $length = null): string
    {
        $result = $length === null ?
            @file_get_contents($fileName, $useIncludePath, $context, $offset) :
            @file_get_contents($fileName, $useIncludePath, $context, $offset, $length);
        if ($result === false) {
            throw new \RuntimeException('Failed to read file ' . $fileName);
        }
        return $result;
    }
}
