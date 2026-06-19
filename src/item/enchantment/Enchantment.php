<?php

declare(strict_types=1);

namespace pocketmine\item\enchantment;

use pocketmine\lang\Translatable;

class Enchantment
{
    private \Closure $minEnchantingPower;

    public function __construct(
        private Translatable|string $name,
        private int $rarity,
        private int $primaryItemFlags,
        private int $secondaryItemFlags,
        private int $maxLevel,
        ?\Closure $minEnchantingPower = null,
        private int $enchantingPowerRange = 50,
    ) {
        $this->minEnchantingPower = $minEnchantingPower ?? static fn(int $level): int => 1;
    }

    public function getName(): Translatable|string { return $this->name; }
    public function getRarity(): int { return $this->rarity; }
    public function getPrimaryItemFlags(): int { return $this->primaryItemFlags; }
    public function getSecondaryItemFlags(): int { return $this->secondaryItemFlags; }
    public function hasPrimaryItemType(int $flag): bool { return ($this->primaryItemFlags & $flag) !== 0; }
    public function hasSecondaryItemType(int $flag): bool { return ($this->secondaryItemFlags & $flag) !== 0; }
    public function getMaxLevel(): int { return $this->maxLevel; }
    public function isCompatibleWith(Enchantment $other): bool { return IncompatibleEnchantmentRegistry::getInstance()->areCompatible($this, $other); }
    public function getMinEnchantingPower(int $level): int { return ($this->minEnchantingPower)($level); }
    public function getMaxEnchantingPower(int $level): int { return $this->getMinEnchantingPower($level) + $this->enchantingPowerRange; }
}
