<?php

declare(strict_types=1);

namespace pocketmine\command\utils;

final class CommandStringHelper
{
    private function __construct()
    {
    }

    /** @return list<string> */
    public static function parseQuoteAware(string $commandLine): array
    {
        $args = [];
        preg_match_all('/"((?:\\\\.|[^\\\\"])*)"|(\\S+)/u', $commandLine, $matches);
        foreach ($matches[0] as $k => $_) {
            for ($i = 1; $i <= 2; ++$i) {
                if (($matches[$i][$k] ?? '') !== '') {
                    $args[] = preg_replace('/\\\\([\\\\"])/u', '$1', $matches[$i][$k]) ?? $matches[$i][$k];
                    break;
                }
            }
        }
        return $args;
    }
}
