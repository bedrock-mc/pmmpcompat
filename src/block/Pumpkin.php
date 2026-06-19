<?php

declare(strict_types=1);

namespace pocketmine\block;

class Pumpkin extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:pumpkin', 'Pumpkin'); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
