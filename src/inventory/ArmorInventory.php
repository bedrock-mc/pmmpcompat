<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\item\Item;

class ArmorInventory extends SimpleInventory
{
    public const SLOT_CHEST = 1;
    public const SLOT_FEET = 3;
    public const SLOT_HEAD = 0;
    public const SLOT_LEGS = 2;

    public function __construct(private ?object $holder = null)
    {
        parent::__construct(4);
    }

    public function getBoots(): Item { return $this->getItem(self::SLOT_FEET); }
    public function getChestplate(): Item { return $this->getItem(self::SLOT_CHEST); }
    public function getHelmet(): Item { return $this->getItem(self::SLOT_HEAD); }
    public function getHolder(): ?object { return $this->holder; }
    public function getLeggings(): Item { return $this->getItem(self::SLOT_LEGS); }
    public function setBoots(Item $boots): void { $this->setItem(self::SLOT_FEET, $boots); }
    public function setChestplate(Item $chestplate): void { $this->setItem(self::SLOT_CHEST, $chestplate); }
    public function setHelmet(Item $helmet): void { $this->setItem(self::SLOT_HEAD, $helmet); }
    public function setLeggings(Item $leggings): void { $this->setItem(self::SLOT_LEGS, $leggings); }
}
