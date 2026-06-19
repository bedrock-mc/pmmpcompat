<?php

declare(strict_types=1);

namespace pocketmine\block;

class DetectorRail extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:detectorrail', 'DetectorRail'); }
    public function isActivated(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setActivated(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
