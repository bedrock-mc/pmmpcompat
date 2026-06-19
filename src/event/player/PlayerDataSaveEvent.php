<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

class PlayerDataSaveEvent extends Event implements Cancellable
{
    use CancellableTrait;

    public function __construct(protected CompoundTag $data, protected string $playerName, private ?Player $player)
    {
    }

    public function getSaveData(): CompoundTag { return $this->data; }
    public function setSaveData(CompoundTag $data): void { $this->data = $data; }
    public function getPlayerName(): string { return $this->playerName; }
    public function getPlayer(): ?Player { return $this->player; }
}
