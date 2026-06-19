<?php

declare(strict_types=1);

namespace pocketmine\utils;

class Git
{
    public static function getRepositoryState(string $dir, bool &$dirty): ?string
    {
        $dirty = false;
        $head = null;
        $code = 0;
        @exec('git -C ' . escapeshellarg($dir) . ' rev-parse HEAD 2>/dev/null', $output, $code);
        if ($code !== 0 || !isset($output[0]) || !preg_match('/^[a-f0-9]{40}$/', trim($output[0]))) {
            return null;
        }
        $head = trim($output[0]);
        @exec('git -C ' . escapeshellarg($dir) . ' diff --quiet 2>/dev/null', result_code: $diffCode);
        @exec('git -C ' . escapeshellarg($dir) . ' diff --cached --quiet 2>/dev/null', result_code: $cachedCode);
        $dirty = $diffCode === 1 || $cachedCode === 1;
        return $head;
    }

    public static function getRepositoryStatePretty(string $dir): string
    {
        $dirty = false;
        $hash = self::getRepositoryState($dir, $dirty);
        return $hash !== null ? $hash . ($dirty ? '-dirty' : '') : str_repeat('0', 40);
    }
}
