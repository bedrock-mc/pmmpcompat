<?php

declare(strict_types=1);

namespace pocketmine\block\inventory;

class LoomInventory extends CompatBlockInventory
{
    public function __construct(mixed ...$args) { parent::__construct($args[0] ?? null, 3); }
    public const SLOT_BANNER = 0;
    public const SLOT_DYE = 1;
    public const SLOT_PATTERN = 2;
}
