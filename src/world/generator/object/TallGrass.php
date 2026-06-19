<?php

declare(strict_types=1);

namespace pocketmine\world\generator\object;

use pocketmine\block\Block;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;

class TallGrass
{
    public static function growGrass(ChunkManager $world, mixed $pos, Random $random, int $count = 15, int $radius = 10): void
    {
        $blocks = [
            new Block('minecraft:dandelion', 'Dandelion'),
            new Block('minecraft:poppy', 'Poppy'),
            new Block('minecraft:tall_grass', 'Tall Grass'),
            new Block('minecraft:tall_grass', 'Tall Grass'),
            new Block('minecraft:tall_grass', 'Tall Grass'),
            new Block('minecraft:tall_grass', 'Tall Grass'),
        ];
        $baseX = (int) floor($pos->x ?? 0);
        $baseY = (int) floor($pos->y ?? 0);
        $baseZ = (int) floor($pos->z ?? 0);
        for ($i = 0; $i < $count; ++$i) {
            $x = $random->nextRange($baseX - $radius, $baseX + $radius);
            $z = $random->nextRange($baseZ - $radius, $baseZ + $radius);
            if ($world->isInWorld($x, $baseY + 1, $z) && $world->getBlockAt($x, $baseY + 1, $z)->getTypeId() === 'minecraft:air') {
                $world->setBlockAt($x, $baseY + 1, $z, $blocks[$random->nextRange(0, count($blocks) - 1)]);
            }
        }
    }
}
