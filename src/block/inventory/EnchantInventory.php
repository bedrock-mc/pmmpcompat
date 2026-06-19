<?php

declare(strict_types=1);

namespace pocketmine\block\inventory;

class EnchantInventory extends CompatBlockInventory
{
    public function __construct(mixed ...$args) { parent::__construct($args[0] ?? null, 2); }
    public const SLOT_INPUT = 0;
    public const SLOT_LAPIS = 1;
    public function getInput(mixed ...$args): mixed { return $this->itemAt(self::SLOT_INPUT); }
    public function getLapis(mixed ...$args): mixed { return $this->itemAt(self::SLOT_LAPIS); }
    public function getOption(mixed ...$args): mixed { return null; }
    public function getOutput(mixed ...$args): mixed { return null; }
}
