<?php

declare(strict_types=1);

namespace pocketmine\block;

class RedstoneWire extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:redstonewire', 'RedstoneWire'); }
    public function asItem(): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, []); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
}
