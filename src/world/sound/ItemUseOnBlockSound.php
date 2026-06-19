<?php

declare(strict_types=1);

namespace pocketmine\world\sound;

class ItemUseOnBlockSound extends SimpleSound
{
    public function __construct(mixed ...$args)
    {
        parent::__construct(...$args);
    }

    public function encode(mixed ...$args): array
    {
        return parent::encode(...$args);
    }

    public function getBlock(mixed ...$args): mixed { return $this->constructorArg(0); }
}
