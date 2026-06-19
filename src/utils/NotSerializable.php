<?php

declare(strict_types=1);

namespace pocketmine\utils;

trait NotSerializable
{
    final public function __serialize(): array
    {
        throw new \LogicException('Serialization of ' . static::class . ' objects is not allowed');
    }

    final public function __unserialize(array $data): void
    {
        throw new \LogicException('Unserialization of ' . static::class . ' objects is not allowed');
    }
}
