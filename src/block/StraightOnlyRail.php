<?php

declare(strict_types=1);

namespace pocketmine\block;

class StraightOnlyRail extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:straightonlyrail', 'StraightOnlyRail'); }
    public function getShape(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setShape(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
