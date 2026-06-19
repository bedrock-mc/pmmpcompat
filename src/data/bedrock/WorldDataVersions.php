<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock;

class WorldDataVersions
{
    public const BLOCK_STATES = (1 << 24) | (21 << 16) | (60 << 8) | 33;
    public const CHUNK = 0;
    public const SUBCHUNK = 0;
    public const STORAGE = 10;
    public const NETWORK = 827;
    public const LAST_OPENED_IN = [1, 21, 100, 23, 0];
}
