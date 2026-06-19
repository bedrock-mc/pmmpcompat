<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class PlayerExperienceChangeEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Player $player,
        private int $oldLevel,
        private float $oldProgress,
        private ?int $newLevel,
        private ?float $newProgress
    ) {
        parent::__construct($player);
    }

    public function getOldLevel(): int { return $this->oldLevel; }
    public function getOldProgress(): float { return $this->oldProgress; }
    public function getNewLevel(): ?int { return $this->newLevel; }
    public function getNewProgress(): ?float { return $this->newProgress; }
    public function setNewLevel(?int $newLevel): void { $this->newLevel = $newLevel; }
    public function setNewProgress(?float $newProgress): void
    {
        if ($newProgress !== null && ($newProgress < 0.0 || $newProgress > 1.0)) {
            throw new \InvalidArgumentException('XP progress must be in range 0-1');
        }
        $this->newProgress = $newProgress;
    }
}
