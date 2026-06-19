<?php

declare(strict_types=1);

namespace pocketmine\world\format\io\region;

class McRegion extends WritableRegionWorldProvider
{
    protected static function getRegionFileExtension(): string
    {
        return 'mcr';
    }

    protected static function getPcWorldFormatVersion(): int
    {
        return 19132;
    }

    public function getWorldMinY(): int
    {
        return 0;
    }

    public function getWorldMaxY(): int
    {
        return 128;
    }
}
