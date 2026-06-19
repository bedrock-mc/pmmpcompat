<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert\property;

use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;

interface Property
{
    public function deserialize(object $block, BlockStateReader $in): void;
    public function getName(): string;
    public function serialize(object $block, BlockStateWriter $out): void;
}
