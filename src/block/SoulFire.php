<?php

declare(strict_types=1);

namespace pocketmine\block;

class SoulFire extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:soulfire', 'SoulFire'); }
    public static function canBeSupportedBy(mixed ...$args): mixed { return self::compatStaticMethod(__FUNCTION__, $args); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
}
