<?php

declare(strict_types=1);

namespace pocketmine\block;

class Froglight extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:froglight', 'Froglight'); }
    public function describeBlockItemState(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function getFroglightType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getLightLevel(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function setFroglightType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
