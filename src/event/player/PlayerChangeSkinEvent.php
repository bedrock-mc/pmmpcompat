<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\entity\Skin;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class PlayerChangeSkinEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Player $player, private Skin $oldSkin, private Skin $newSkin)
    {
        parent::__construct($player);
    }

    public function getOldSkin(): Skin { return $this->oldSkin; }
    public function getNewSkin(): Skin { return $this->newSkin; }
    public function setNewSkin(Skin $skin): void { $this->newSkin = $skin; }
}
