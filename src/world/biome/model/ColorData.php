<?php

declare(strict_types=1);

namespace pocketmine\world\biome\model;

class ColorData
{
    public function __construct(
        public int $r = 0,
        public int $g = 0,
        public int $b = 0,
        public int $a = 255
    ) {}
}
