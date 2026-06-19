<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\player\Player;

class PlayerDisplayNameChangeEvent extends PlayerEvent
{
    public function __construct(Player $player, private string $oldName, private string $newName)
    {
        parent::__construct($player);
    }

    public function getOldName(): string { return $this->oldName; }
    public function getNewName(): string { return $this->newName; }
}
