<?php

declare(strict_types=1);

namespace pocketmine\item;

class ArmorTypeInfo extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:armortypeinfo', 'ArmorTypeInfo'); }
    public function getArmorSlot(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getDefensePoints(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getMaterial(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaxDurability(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getToughness(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isFireProof(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
