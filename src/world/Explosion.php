<?php

declare(strict_types=1);

namespace pocketmine\world;

use pocketmine\math\Vector3;

class Explosion
{
    public const DEFAULT_FIRE_CHANCE = 1.0 / 3.0;

    /** @var array<string, Vector3> */
    public array $affectedBlocks = [];
    public World $world;

    public function __construct(
        public Position $source,
        public float $radius,
        private mixed $what = null,
        private float $fireChance = 0.0,
    ) {
        if (!$source->isValid()) {
            throw new \InvalidArgumentException('Position does not have a valid world');
        }
        if ($radius <= 0) {
            throw new \InvalidArgumentException('Explosion radius must be greater than 0');
        }
        $this->world = $source->getWorld();
        $this->setFireChance($fireChance);
    }

    public function setFireChance(float $fireChance): void
    {
        if ($fireChance < 0.0 || $fireChance > 1.0 || is_nan($fireChance)) {
            throw new \InvalidArgumentException('Fire chance must be between 0 and 1');
        }
        $this->fireChance = $fireChance;
    }

    public function explodeA(): bool
    {
        $r = (int) ceil($this->radius);
        for ($x = -$r; $x <= $r; $x++) {
            for ($y = -$r; $y <= $r; $y++) {
                for ($z = -$r; $z <= $r; $z++) {
                    if (($x ** 2 + $y ** 2 + $z ** 2) <= $this->radius ** 2) {
                        $pos = new Vector3(floor($this->source->x + $x), floor($this->source->y + $y), floor($this->source->z + $z));
                        $this->affectedBlocks[World::blockHash((int) $pos->x, (int) $pos->y, (int) $pos->z)] = $pos;
                    }
                }
            }
        }
        return $this->affectedBlocks !== [];
    }

    public function explodeB(): bool
    {
        foreach ($this->affectedBlocks as $pos) {
            $this->world->setBlock($pos, \pocketmine\block\VanillaBlocks::AIR());
        }
        return true;
    }
}
