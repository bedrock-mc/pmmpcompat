<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;

class PlayerLoginEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Player $player, protected Translatable|string $kickMessage)
    {
        parent::__construct($player);
    }

    public function setKickMessage(Translatable|string $kickMessage): void
    {
        $this->kickMessage = $kickMessage;
    }

    public function getKickMessage(): Translatable|string
    {
        return $this->kickMessage;
    }
}
