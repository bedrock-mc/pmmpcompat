<?php

declare(strict_types=1);

namespace pocketmine\console;

final class ConsoleReader
{
    /** @var resource|null */
    private $stdin = null;

    public function __construct() {}

    public function readLine(): ?string
    {
        $this->stdin ??= fopen('php://stdin', 'r') ?: null;
        if ($this->stdin === null) {
            return null;
        }

        $read = [$this->stdin];
        $write = $except = null;
        $ready = @stream_select($read, $write, $except, 0, 0);
        if ($ready !== 1) {
            return null;
        }

        $line = fgets($this->stdin);
        if ($line === false) {
            return null;
        }
        $line = trim($line);
        return $line !== '' ? $line : null;
    }

    public function __destruct()
    {
        if (is_resource($this->stdin)) {
            fclose($this->stdin);
        }
    }
}
