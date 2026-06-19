<?php

declare(strict_types=1);

namespace pocketmine\block\inventory;

class ChestInventory extends CompatBlockInventory
{
    public function __construct(mixed ...$args) { parent::__construct($args[0] ?? null, 27); }
    public function animateBlock(mixed ...$args): mixed { return null; }
}
