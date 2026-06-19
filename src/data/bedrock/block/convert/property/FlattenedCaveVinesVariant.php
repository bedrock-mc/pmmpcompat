<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert\property;

enum FlattenedCaveVinesVariant: string
{
    case NO_BERRIES = '';
    case HEAD_WITH_BERRIES = '_head_with_berries';
    case BODY_WITH_BERRIES = '_body_with_berries';
}
