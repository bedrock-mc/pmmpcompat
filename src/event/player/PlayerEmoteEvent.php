<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class PlayerEmoteEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Player $player, private string $emoteId)
    {
        parent::__construct($player);
    }

    public function getEmoteId(): string { return $this->emoteId; }
    public function setEmoteId(string $emoteId): void { $this->emoteId = $emoteId; }
}
