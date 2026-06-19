<?php

declare(strict_types=1);

namespace pocketmine\item;

class FireworkRocketExplosion extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:fireworkrocketexplosion', 'FireworkRocketExplosion'); }
    public static function fromCompoundTag(mixed ...$args): mixed { return self::compatStaticMethod(__FUNCTION__, $args); }
    public function getColorMix(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getColors(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getFadeColors(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getFlashColor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getTrail(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function toCompoundTag(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function willTwinkle(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
