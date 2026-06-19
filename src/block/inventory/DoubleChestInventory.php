<?php

declare(strict_types=1);

namespace pocketmine\block\inventory;

class DoubleChestInventory extends CompatBlockInventory
{
    private ?ChestInventory $left = null;
    private ?ChestInventory $right = null;

    public function __construct(mixed ...$args) {
        $this->left = $args[0] instanceof ChestInventory ? $args[0] : null;
        $this->right = $args[1] instanceof ChestInventory ? $args[1] : null;
        parent::__construct($this->left?->getHolder(), 54);
    }

    public function getContents(bool $includeEmpty = false): array { return parent::getContents($includeEmpty); }
    public function getInventory(mixed ...$args): mixed { return $this; }
    public function getItem(int $index): \pocketmine\item\Item { return parent::getItem($index); }
    public function getLeftSide(mixed ...$args): mixed { return $this->left ?? null; }
    public function getRightSide(mixed ...$args): mixed { return $this->right ?? null; }
    public function getSize(): int { return parent::getSize(); }
    public function isSlotEmpty(int $index): bool { return parent::isSlotEmpty($index); }
}
