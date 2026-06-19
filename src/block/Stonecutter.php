<?php

declare(strict_types=1);

namespace pocketmine\block;

class Stonecutter extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:stonecutter', 'Stonecutter'); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
