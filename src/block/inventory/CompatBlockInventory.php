<?php

declare(strict_types=1);

namespace pocketmine\block\inventory;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\world\Position;

class CompatBlockInventory extends Inventory implements BlockInventory
{
    protected Position $holder;

    public function __construct(mixed $holder = null, int $size = 27)
    {
        $this->holder = $holder instanceof Position ? $holder : new Position(0, 0, 0, null);
        parent::__construct($size);
    }

    public function getHolder(): Position
    {
        return $this->holder;
    }

    public function getViewerCount(): int
    {
        return count($this->getViewers());
    }

    public function animateBlock(mixed ...$args): mixed
    {
        return null;
    }

    protected function itemAt(int $slot): Item
    {
        return $this->getItem($slot);
    }

    protected function setItemAt(int $slot, mixed $item): mixed
    {
        if ($item instanceof Item) {
            $this->setItem($slot, $item);
        }
        return null;
    }
}
