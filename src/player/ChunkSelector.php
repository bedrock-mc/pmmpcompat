<?php

declare(strict_types=1);

namespace pocketmine\player;

use pocketmine\world\World;

final class ChunkSelector
{
    public function selectChunks(int $radius, int $centerX, int $centerZ): \Generator
    {
        for ($subRadius = 0; $subRadius < $radius; $subRadius++) {
            $subRadiusSquared = $subRadius ** 2;
            $nextSubRadiusSquared = ($subRadius + 1) ** 2;
            $minX = (int) ($subRadius / M_SQRT2);
            $lastZ = 0;

            for ($x = $subRadius; $x >= $minX; --$x) {
                for ($z = $lastZ; $z <= $x; ++$z) {
                    $distanceSquared = ($x ** 2 + $z ** 2);
                    if ($distanceSquared < $subRadiusSquared) {
                        continue;
                    }
                    if ($distanceSquared >= $nextSubRadiusSquared) {
                        break;
                    }

                    $lastZ = $z;
                    yield $subRadius => World::chunkHash($centerX + $x, $centerZ + $z);
                    yield $subRadius => World::chunkHash($centerX - $x - 1, $centerZ + $z);
                    yield $subRadius => World::chunkHash($centerX + $x, $centerZ - $z - 1);
                    yield $subRadius => World::chunkHash($centerX - $x - 1, $centerZ - $z - 1);

                    if ($x !== $z) {
                        yield $subRadius => World::chunkHash($centerX + $z, $centerZ + $x);
                        yield $subRadius => World::chunkHash($centerX - $z - 1, $centerZ + $x);
                        yield $subRadius => World::chunkHash($centerX + $z, $centerZ - $x - 1);
                        yield $subRadius => World::chunkHash($centerX - $z - 1, $centerZ - $x - 1);
                    }
                }
            }
        }
    }
}
