<?php

declare(strict_types=1);

namespace pocketmine\item;

class Armor extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:armor', 'Armor'); }
    public const TAG_CUSTOM_COLOR = 0;
    public function clearCustomColor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getArmorSlot(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getCustomColor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getDefensePoints(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getEnchantability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getEnchantmentProtectionFactor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaterial(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaxDurability(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function isFireProof(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onClickAir(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setCustomColor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
