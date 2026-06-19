<?php

declare(strict_types=1);

namespace pocketmine\item;

class ArmorMaterial extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:armormaterial', 'ArmorMaterial'); }
    public function getEnchantability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getEquipSound(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
