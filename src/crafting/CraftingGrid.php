<?php

declare(strict_types=1);

namespace pocketmine\crafting;

use pocketmine\inventory\SimpleInventory;
use pocketmine\item\Item;

class CraftingGrid extends SimpleInventory
{
    public const SIZE_BIG = 3;
    public const SIZE_SMALL = 2;

    private ?int $startX = null;
    private ?int $xLen = null;
    private ?int $startY = null;
    private ?int $yLen = null;

    public function __construct(private int $gridWidth = self::SIZE_SMALL)
    {
        parent::__construct($gridWidth ** 2);
    }

    public function getGridWidth(): int { return $this->gridWidth; }

    public function getIngredient(int $x, int $y): Item
    {
        if ($this->startX === null || $this->startY === null) {
            throw new \LogicException('No ingredients found in grid');
        }
        return $this->getItem(($y + $this->startY) * $this->gridWidth + ($x + $this->startX));
    }

    public function getRecipeHeight(): int { return $this->yLen ?? 0; }
    public function getRecipeWidth(): int { return $this->xLen ?? 0; }

    public function setItem(int $index, Item $item, bool $syncHost = true): void
    {
        parent::setItem($index, $item, $syncHost);
        $this->seekRecipeBounds();
    }

    private function seekRecipeBounds(): void
    {
        $minX = PHP_INT_MAX;
        $maxX = 0;
        $minY = PHP_INT_MAX;
        $maxY = 0;
        $empty = true;
        for ($y = 0; $y < $this->gridWidth; $y++) {
            for ($x = 0; $x < $this->gridWidth; $x++) {
                if (!$this->isSlotEmpty($y * $this->gridWidth + $x)) {
                    $minX = min($minX, $x);
                    $maxX = max($maxX, $x);
                    $minY = min($minY, $y);
                    $maxY = max($maxY, $y);
                    $empty = false;
                }
            }
        }
        if ($empty) {
            $this->startX = $this->xLen = $this->startY = $this->yLen = null;
            return;
        }
        $this->startX = $minX;
        $this->xLen = $maxX - $minX + 1;
        $this->startY = $minY;
        $this->yLen = $maxY - $minY + 1;
    }
}
