<?php

declare(strict_types=1);

namespace pocketmine\block;

class Anvil extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:anvil', 'Anvil'); }
    public const SLIGHTLY_DAMAGED = 0;
    public const UNDAMAGED = 0;
    public const VERY_DAMAGED = 0;
    public function describeBlockItemState(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function getDamage(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getFallDamagePerBlock(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getLandSound(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaxFallDamage(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onHitGround(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setDamage(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
