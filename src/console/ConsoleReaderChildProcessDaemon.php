<?php

declare(strict_types=1);

namespace pocketmine\console;

final class ConsoleReaderChildProcessDaemon
{
    public const TOKEN_DELIMITER = ':';
    public const TOKEN_HASH_ALGO = 'xxh3';

    public function __construct(mixed ...$args) {}

    public function readLine(): ?string
    {
        return null;
    }

    public function quit(): void {}
}
