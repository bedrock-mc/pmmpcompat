<?php

declare(strict_types=1);

namespace pocketmine\player;

use pocketmine\utils\LegacyEnumShimTrait;

enum UsedChunkStatus
{
    use LegacyEnumShimTrait;

    case NEEDED;
    case REQUESTED_GENERATION;
    case REQUESTED_SENDING;
    case SENT;
}
