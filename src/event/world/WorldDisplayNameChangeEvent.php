<?php

declare(strict_types=1);

namespace pocketmine\event\world;

use pocketmine\world\World;

final class WorldDisplayNameChangeEvent extends WorldEvent
{
    public function __construct(World $world, private string $oldName, private string $newName)
    {
        parent::__construct($world);
    }

    public function getOldName(): string { return $this->oldName; }
    public function getNewName(): string { return $this->newName; }
}
