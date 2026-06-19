<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;

class PlayerKickEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;
    use PlayerDisconnectEventTrait;

    public function __construct(
        Player $player,
        protected Translatable|string $disconnectReason,
        protected Translatable|string $quitMessage,
        protected Translatable|string|null $disconnectScreenMessage
    ) {
        parent::__construct($player);
    }

    public function setQuitMessage(Translatable|string $quitMessage): void
    {
        $this->quitMessage = $quitMessage;
    }

    public function getQuitMessage(): Translatable|string
    {
        return $this->quitMessage;
    }
}
