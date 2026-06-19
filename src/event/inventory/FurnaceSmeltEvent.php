<?php

declare(strict_types=1);

namespace pocketmine\event\inventory;

use pocketmine\block\tile\Furnace;
use pocketmine\event\block\BlockEvent;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\item\Item;

class FurnaceSmeltEvent extends BlockEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        private Furnace $furnace,
        private Item $source,
        private Item $result,
    ) {
        parent::__construct($furnace->getBlock());
        $this->source = clone $source;
        $this->source->setCount(1);
    }

    public function getFurnace(): Furnace { return $this->furnace; }
    public function getSource(): Item { return $this->source; }
    public function getResult(): Item { return $this->result; }
    public function setResult(Item $result): void { $this->result = $result; }
}
