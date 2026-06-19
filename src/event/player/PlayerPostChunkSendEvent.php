<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\player\Player;

final class PlayerPostChunkSendEvent extends PlayerEvent
{
    public function __construct(Player $player, private int $chunkX, private int $chunkZ)
    {
        parent::__construct($player);
    }

    public function getChunkX(): int { return $this->chunkX; }
    public function getChunkZ(): int { return $this->chunkZ; }
}
