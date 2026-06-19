<?php

declare(strict_types=1);

namespace pocketmine\block;

class Lantern extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:lantern', 'Lantern'); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isHanging(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setHanging(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
