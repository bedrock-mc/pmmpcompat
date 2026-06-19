<?php

declare(strict_types=1);

namespace pocketmine\block\inventory;

class StonecutterInventory extends CompatBlockInventory
{
    public function __construct(mixed ...$args) { parent::__construct($args[0] ?? null, 1); }
    public const SLOT_INPUT = 0;
}
