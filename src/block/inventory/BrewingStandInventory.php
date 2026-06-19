<?php

declare(strict_types=1);

namespace pocketmine\block\inventory;

class BrewingStandInventory extends CompatBlockInventory
{
    public function __construct(mixed ...$args) { parent::__construct($args[0] ?? null, 5); }
    public const SLOT_INGREDIENT = 0;
    public const SLOT_BOTTLE_LEFT = 1;
    public const SLOT_BOTTLE_MIDDLE = 2;
    public const SLOT_BOTTLE_RIGHT = 3;
    public const SLOT_FUEL = 4;
}
