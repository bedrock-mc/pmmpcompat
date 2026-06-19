<?php

declare(strict_types=1);

namespace pocketmine\world\format\io;

class FastChunkSerializer
{
    public static function deserializeTerrain(string $data): mixed
    {
        $decoded = @unserialize($data);
        return $decoded === false && $data !== serialize(false) ? null : $decoded;
    }
    public static function serializeTerrain(mixed $chunk): string { return serialize($chunk); }
}
