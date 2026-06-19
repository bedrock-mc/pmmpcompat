<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\Block;
use pocketmine\block\Farmland;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class FarmlandHydrationChangeEvent extends BlockEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Block $block,
        private int $oldHydration,
        private int $newHydration,
    ) {
        parent::__construct($block);
    }

    public function getOldHydration(): int
    {
        return $this->oldHydration;
    }

    public function getNewHydration(): int
    {
        return $this->newHydration;
    }

    public function setNewHydration(int $hydration): void
    {
        if ($hydration < 0 || $hydration > Farmland::MAX_WETNESS) {
            throw new \InvalidArgumentException('Hydration must be in range 0 ... ' . Farmland::MAX_WETNESS);
        }
        $this->newHydration = $hydration;
    }
}
