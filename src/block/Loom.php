<?php

declare(strict_types=1);

namespace pocketmine\block;

class Loom extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:loom', 'Loom'); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
