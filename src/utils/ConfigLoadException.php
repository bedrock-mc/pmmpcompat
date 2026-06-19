<?php

declare(strict_types=1);

namespace pocketmine\utils;

final class ConfigLoadException extends \RuntimeException
{
    public static function wrap(string $fileName, \Exception $e): self
    {
        return new self('Failed to parse config ' . $fileName . ': ' . $e->getMessage(), 0, $e);
    }
}
