<?php

declare(strict_types=1);

namespace pocketmine\block;

class Chest extends Block
{
    public function __construct()
    {
        parent::__construct('minecraft:chest', 'Chest');
    }

    public function getFuelTime(): int
    {
        return 300;
    }

    public function getSupportType(mixed ...$args): mixed
    {
        return null;
    }

    public function onInteract(mixed ...$args): bool
    {
        return true;
    }

    public function onPostPlace(): void {}
}
