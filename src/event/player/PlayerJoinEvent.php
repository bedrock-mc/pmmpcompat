<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\player\Player;

class PlayerJoinEvent extends PlayerEvent
{
    public function __construct(Player $player, private string $joinMessage = '')
    {
        parent::__construct($player);
    }

    public function getJoinMessage(): string
    {
        return $this->joinMessage;
    }

    public function setJoinMessage(string $message): void
    {
        $this->joinMessage = $message;
    }
}
