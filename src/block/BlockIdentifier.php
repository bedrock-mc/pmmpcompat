<?php

declare(strict_types=1);

namespace pocketmine\block;

class BlockIdentifier extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:blockidentifier', 'BlockIdentifier'); }
    public function getBlockTypeId(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getTileClass(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
