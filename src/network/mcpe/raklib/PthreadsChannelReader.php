<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\raklib;

class PthreadsChannelReader
{
    private mixed $buffer;

    /** @param array<int, string>|\ArrayAccess<int, string> $buffer */
    public function __construct(mixed &$buffer)
    {
        $this->buffer =& $buffer;
    }

    public function read(): ?string
    {
        if (is_array($this->buffer)) {
            return array_shift($this->buffer);
        }
        if (is_object($this->buffer) && method_exists($this->buffer, 'shift')) {
            $value = $this->buffer->shift();
            return is_string($value) ? $value : null;
        }
        return null;
    }
}
