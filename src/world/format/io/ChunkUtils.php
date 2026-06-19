<?php

declare(strict_types=1);

namespace pocketmine\world\format\io;

class ChunkUtils
{
    public static function convertBiomeColors(array $array): string
    {
        $result = str_repeat("\x00", 256);
        foreach ($array as $i => $color) {
            if ($i >= 0 && $i < 256) {
                $result[$i] = chr(((int) $color >> 24) & 0xff);
            }
        }
        return $result;
    }
    public static function extrapolate3DBiomes(string $biomes2d): array
    {
        if (strlen($biomes2d) !== 256) {
            throw new \InvalidArgumentException('Biome array is expected to be exactly 256 bytes');
        }
        $result = [];
        for ($y = 0; $y < 16; $y++) {
            $result[$y] = $biomes2d;
        }
        return $result;
    }
}
