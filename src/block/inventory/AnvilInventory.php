<?php

declare(strict_types=1);

namespace pocketmine\block\inventory;

class AnvilInventory extends CompatBlockInventory
{
    public function __construct(mixed ...$args) { parent::__construct($args[0] ?? null, 2); }
    public const SLOT_INPUT = 0;
    public const SLOT_MATERIAL = 1;
}
