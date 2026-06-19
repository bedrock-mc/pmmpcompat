<?php

declare(strict_types=1);

namespace pocketmine\block;

class Lava extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:lava', 'Lava'); }
    public function getBucketEmptySound(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getBucketFillSound(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getFlowDecayPerBlock(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function onEntityInside(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function tickRate(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
