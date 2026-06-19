<?php

declare(strict_types=1);

namespace pocketmine\utils;

final class Process
{
    private function __construct() {}

    /** @return array{int, int, int} */
    public static function getAdvancedMemoryUsage(): array
    {
        $reserved = memory_get_usage();
        return [$reserved, memory_get_usage(), memory_get_usage(true)];
    }

    public static function getMemoryUsage(): int
    {
        return self::getAdvancedMemoryUsage()[1];
    }

    /** @return array{int, int} */
    public static function getRealMemoryUsage(): array
    {
        return [memory_get_usage(true), 0];
    }

    public static function getThreadCount(): int
    {
        if (is_file('/proc/self/status')) {
            $status = @file_get_contents('/proc/self/status');
            if (is_string($status) && preg_match('/Threads:[ \t]+([0-9]+)/', $status, $matches) === 1) {
                return (int) $matches[1];
            }
        }
        return 1;
    }

    public static function kill(int $pid, bool $subprocesses = false): void
    {
        if (function_exists('posix_kill')) {
            posix_kill($subprocesses ? -$pid : $pid, 9);
            return;
        }
        exec('kill -9 ' . escapeshellarg((string) ($subprocesses ? -$pid : $pid)) . ' > /dev/null 2>&1');
    }

    public static function execute(string $command, ?string &$stdout = null, ?string &$stderr = null): int
    {
        $process = proc_open($command, [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes);
        if ($process === false) {
            $stdout = '';
            $stderr = 'Failed to open process';
            return -1;
        }
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]) ?: '';
        $stderr = stream_get_contents($pipes[2]) ?: '';
        fclose($pipes[1]);
        fclose($pipes[2]);
        return proc_close($process);
    }

    public static function pid(): int
    {
        $pid = getmypid();
        if ($pid === false) {
            throw new \LogicException("getmypid() doesn't work on this platform");
        }
        return $pid;
    }

    public static function uid(): int
    {
        $uid = getmyuid();
        if ($uid === false) {
            throw new \LogicException("getmyuid() doesn't work on this platform");
        }
        return $uid;
    }
}
