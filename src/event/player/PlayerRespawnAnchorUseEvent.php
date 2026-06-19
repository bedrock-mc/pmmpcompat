<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\block\Block;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class PlayerRespawnAnchorUseEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public const ACTION_EXPLODE = 0;
    public const ACTION_SET_SPAWN = 1;

    public function __construct(
        Player $player,
        protected Block $block,
        private int $action = self::ACTION_EXPLODE,
    ) {
        parent::__construct($player);
    }

    public function getBlock(): Block
    {
        return $this->block;
    }

    public function getAction(): int
    {
        return $this->action;
    }

    public function setAction(int $action): void
    {
        $this->action = $action;
    }
}
