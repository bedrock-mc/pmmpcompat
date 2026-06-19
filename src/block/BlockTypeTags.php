<?php

declare(strict_types=1);

namespace pocketmine\block;

class BlockTypeTags extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:blocktypetags', 'BlockTypeTags'); }
    public const DIRT = 0;
    public const FIRE = 0;
    public const HANGING_SIGN = 0;
    public const MUD = 0;
    public const POTTABLE_PLANTS = 0;
    public const SAND = 0;
}
