<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\raklib;

class PthreadsChannelWriter
{
    /** @param array<int, string>|\ArrayAccess<int, string> $buffer */
    public function __construct(private mixed &$buffer) {}

    public function write(string $str): void
    {
        $this->buffer[] = $str;
    }
}
