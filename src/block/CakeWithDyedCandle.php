<?php

declare(strict_types=1);

namespace pocketmine\block;

class CakeWithDyedCandle extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:cakewithdyedcandle', 'CakeWithDyedCandle'); }
    public function getCandle(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
