<?php

declare(strict_types=1);

namespace pocketmine\block\inventory;

class FurnaceInventory extends CompatBlockInventory
{
    public function __construct(mixed ...$args) { parent::__construct($args[0] ?? null, 3); }
    public const SLOT_INPUT = 0;
    public const SLOT_FUEL = 1;
    public const SLOT_RESULT = 2;
    public function getFuel(mixed ...$args): mixed { return $this->itemAt(self::SLOT_FUEL); }
    public function getFurnaceType(mixed ...$args): mixed { return $this->type ?? null; }
    public function getResult(mixed ...$args): mixed { return $this->itemAt(self::SLOT_RESULT); }
    public function getSmelting(mixed ...$args): mixed { return $this->itemAt(self::SLOT_INPUT); }
    public function setFuel(mixed ...$args): mixed { return $this->setItemAt(self::SLOT_FUEL, $args[0] ?? null); }
    public function setResult(mixed ...$args): mixed { return $this->setItemAt(self::SLOT_RESULT, $args[0] ?? null); }
    public function setSmelting(mixed ...$args): mixed { return $this->setItemAt(self::SLOT_INPUT, $args[0] ?? null); }
}
