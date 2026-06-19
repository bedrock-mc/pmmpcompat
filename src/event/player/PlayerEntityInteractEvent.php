<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class PlayerEntityInteractEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Player $player, private Entity $entity, private Vector3 $clickPos)
    {
        parent::__construct($player);
    }

    public function getEntity(): Entity { return $this->entity; }
    public function getClickPosition(): Vector3 { return $this->clickPos; }
}
