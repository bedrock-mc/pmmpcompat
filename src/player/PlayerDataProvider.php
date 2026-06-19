<?php

declare(strict_types=1);

namespace pocketmine\player;

use pocketmine\nbt\tag\CompoundTag;

/**
 * Handles storage of player data. Implementations treat names case-insensitively.
 */
interface PlayerDataProvider
{
    public function hasData(string $name): bool;

    public function loadData(string $name): ?CompoundTag;

    public function saveData(string $name, CompoundTag $data): void;
}
