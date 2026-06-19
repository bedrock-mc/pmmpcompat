<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

interface Container
{
    public const TAG_ITEMS = 0;
    public const TAG_LOCK = 0;

    public function canOpenWith(mixed ...$args): mixed;
    public function getRealInventory(mixed ...$args): mixed;
}
