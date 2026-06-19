<?php

declare(strict_types=1);

namespace pocketmine\world\generator;

use pocketmine\utils\Random;
use pocketmine\utils\Utils;
use pocketmine\world\ChunkManager;

abstract class Generator
{
    protected Random $random;

    public function __construct(
        protected int $seed,
        protected string $preset,
    ) {
        $this->random = new Random($seed);
    }

    public static function convertSeed(string $seed): ?int
    {
        if ($seed === '') {
            return null;
        }
        if (preg_match('/^-?\d+$/', $seed) === 1) {
            return (int) $seed;
        }
        return Utils::javaStringHash($seed);
    }

    abstract public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void;
    abstract public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void;
}
