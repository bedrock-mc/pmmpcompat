<?php

declare(strict_types=1);

namespace pocketmine\item;

class VanillaArmorMaterials extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:vanillaarmormaterials', 'VanillaArmorMaterials'); }
    public static function getAll(mixed ...$args): mixed { return self::compatStaticMethod(__FUNCTION__, $args); }
}
