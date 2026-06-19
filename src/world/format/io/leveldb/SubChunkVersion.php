<?php

declare(strict_types=1);

namespace pocketmine\world\format\io\leveldb;

class SubChunkVersion
{
    public const CLASSIC = 0;
    public const PALETTED_SINGLE = 1;
    public const CLASSIC_BUG_2 = 2;
    public const CLASSIC_BUG_3 = 3;
    public const CLASSIC_BUG_4 = 4;
    public const CLASSIC_BUG_5 = 5;
    public const CLASSIC_BUG_6 = 6;
    public const CLASSIC_BUG_7 = 7;
    public const PALETTED_MULTI = 8;
    public const PALETTED_MULTI_WITH_OFFSET = 9;
}
