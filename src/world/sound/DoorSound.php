<?php

declare(strict_types=1);

namespace pocketmine\world\sound;

class DoorSound extends SimpleSound
{
    public function __construct(mixed ...$args)
    {
        parent::__construct(...$args);
    }

    public function encode(mixed ...$args): array
    {
        return parent::encode(...$args);
    }

    public function getPitch(mixed ...$args): mixed { return (float) $this->constructorArg(0, 0.0); }
}
