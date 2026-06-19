<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\item\Item;

class SimpleInventory extends BaseInventory
{
    public function __construct(int $size = 36)
    {
        parent::__construct($size);
    }

    /** @return array<int, Item> */
    public function getContents(bool $includeEmpty = false): array { return parent::getContents($includeEmpty); }
    public function getItem(int $index): Item { return parent::getItem($index); }
    public function getSize(): int { return parent::getSize(); }
    public function isSlotEmpty(int $index): bool { return parent::isSlotEmpty($index); }
}
