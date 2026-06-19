<?php

declare(strict_types=1);

namespace pocketmine\event\inventory;

use pocketmine\block\tile\Furnace;
use pocketmine\event\block\BlockEvent;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\item\Item;

class FurnaceBurnEvent extends BlockEvent implements Cancellable
{
    use CancellableTrait;

    private bool $burning = true;

    public function __construct(
        private Furnace $furnace,
        private Item $fuel,
        private int $burnTime,
    ) {
        parent::__construct($furnace->getBlock());
    }

    public function getFurnace(): Furnace { return $this->furnace; }
    public function getFuel(): Item { return $this->fuel; }
    public function getBurnTime(): int { return $this->burnTime; }
    public function setBurnTime(int $burnTime): void { $this->burnTime = $burnTime; }
    public function isBurning(): bool { return $this->burning; }
    public function setBurning(bool $burning): void { $this->burning = $burning; }
}
