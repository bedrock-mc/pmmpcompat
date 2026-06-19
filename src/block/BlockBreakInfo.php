<?php

declare(strict_types=1);

namespace pocketmine\block;

class BlockBreakInfo extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:blockbreakinfo', 'BlockBreakInfo'); }
    public const COMPATIBLE_TOOL_MULTIPLIER = 0;
    public const INCOMPATIBLE_TOOL_MULTIPLIER = 0;
    public static function axe(mixed ...$args): mixed { return self::compatStaticMethod(__FUNCTION__, $args); }
    public function breaksInstantly(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getBlastResistance(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getBreakTime(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getHardness(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getToolHarvestLevel(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getToolType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public static function indestructible(mixed ...$args): mixed { return self::compatStaticMethod(__FUNCTION__, $args); }
    public static function instant(mixed ...$args): mixed { return self::compatStaticMethod(__FUNCTION__, $args); }
    public function isBreakable(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isToolCompatible(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public static function pickaxe(mixed ...$args): mixed { return self::compatStaticMethod(__FUNCTION__, $args); }
    public static function shovel(mixed ...$args): mixed { return self::compatStaticMethod(__FUNCTION__, $args); }
    public static function tier(mixed ...$args): mixed { return self::compatStaticMethod(__FUNCTION__, $args); }
}
