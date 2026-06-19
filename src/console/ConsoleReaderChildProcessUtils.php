<?php

declare(strict_types=1);

namespace pocketmine\console;

final class ConsoleReaderChildProcessUtils
{
    public const TOKEN_DELIMITER = ':';
    public const TOKEN_HASH_ALGO = 'xxh3';

    private function __construct() {}

    public static function createMessage(string $line, int &$counter): string
    {
        $token = hash(self::TOKEN_HASH_ALGO, $line, options: ['seed' => $counter]);
        ++$counter;
        return $line . self::TOKEN_DELIMITER . $token;
    }

    public static function parseMessage(string $message, int &$counter): ?string
    {
        $delimiterPos = strrpos($message, self::TOKEN_DELIMITER);
        if ($delimiterPos === false) {
            return null;
        }

        $line = substr($message, 0, $delimiterPos);
        $token = substr($message, $delimiterPos + strlen(self::TOKEN_DELIMITER));
        if (hash(self::TOKEN_HASH_ALGO, $line, options: ['seed' => $counter]) !== $token) {
            return null;
        }
        ++$counter;
        return $line;
    }
}
