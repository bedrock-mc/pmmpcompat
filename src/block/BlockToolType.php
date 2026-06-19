<?php

declare(strict_types=1);

namespace pocketmine\block;

class BlockToolType extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:blocktooltype', 'BlockToolType'); }
    public const AXE = 0;
    public const HOE = 0;
    public const NONE = 0;
    public const PICKAXE = 0;
    public const SHEARS = 0;
    public const SHOVEL = 0;
    public const SWORD = 0;
}
