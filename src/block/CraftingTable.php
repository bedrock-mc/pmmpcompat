<?php

declare(strict_types=1);

namespace pocketmine\block;

class CraftingTable extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:craftingtable', 'CraftingTable'); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
