<?php

declare(strict_types=1);

namespace pocketmine\block;

class Slime extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:slime', 'Slime'); }
    public function getFrictionFactor(): float { return $this->compatMethod(__FUNCTION__, []); }
    public function onEntityLand(mixed ...$args): ?float { return $this->compatMethod(__FUNCTION__, $args); }
}
