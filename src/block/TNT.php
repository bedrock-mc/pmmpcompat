<?php

declare(strict_types=1);

namespace pocketmine\block;

class TNT extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:tnt', 'TNT'); }
    public function describeBlockItemState(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function getFlameEncouragement(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFlammability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function ignite(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isUnstable(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onBreak(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onIncinerate(): void { $this->compatMethod(__FUNCTION__, []); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onProjectileHit(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function setUnstable(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setWorksUnderwater(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function worksUnderwater(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
