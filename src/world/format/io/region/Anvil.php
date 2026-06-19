<?php

declare(strict_types=1);

namespace pocketmine\world\format\io\region;

class Anvil extends WritableRegionWorldProvider
{
    use LegacyAnvilChunkTrait;

    protected static function getRegionFileExtension(): string
    {
        return 'mca';
    }

    protected static function getPcWorldFormatVersion(): int
    {
        return 19133;
    }

    public function getWorldMinY(): int
    {
        return 0;
    }

    public function getWorldMaxY(): int
    {
        return 256;
    }
}
