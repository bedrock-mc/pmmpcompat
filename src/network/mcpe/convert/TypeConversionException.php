<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

class TypeConversionException extends \RuntimeException
{
    public static function wrap(\Throwable $previous, string $message = ''): self
    {
        return new self($message !== '' ? $message : $previous->getMessage(), 0, $previous);
    }
}
