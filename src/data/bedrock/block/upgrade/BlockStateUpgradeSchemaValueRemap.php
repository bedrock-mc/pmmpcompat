<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\upgrade;

use pocketmine\nbt\tag\Tag;

final class BlockStateUpgradeSchemaValueRemap
{
    public function __construct(
        public readonly Tag $old,
        public readonly Tag $new
    ) {}
}
