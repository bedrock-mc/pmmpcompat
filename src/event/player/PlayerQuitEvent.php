<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\lang\Translatable;
use pocketmine\player\Player;

class PlayerQuitEvent extends PlayerEvent
{
    public function __construct(Player $player, private Translatable|string $quitMessage = '', private Translatable|string $quitReason = '')
    {
        parent::__construct($player);
    }

    public function getQuitMessage(): Translatable|string
    {
        return $this->quitMessage;
    }

    public function setQuitMessage(Translatable|string $message): void
    {
        $this->quitMessage = $message;
    }

    public function getQuitReason(): Translatable|string
    {
        return $this->quitReason;
    }
}
