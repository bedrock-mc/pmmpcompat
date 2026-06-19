<?php

declare(strict_types=1);

namespace pocketmine\world;

use pocketmine\math\Vector3;
use pocketmine\utils\Utils;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\normal\Normal;

use function random_int;

/**
 * User-customizable settings for creating PHP-local PMMP-compatible worlds.
 */
final class WorldCreationOptions
{
    /** @phpstan-var class-string<Generator> */
    private string $generatorClass = Normal::class;
    private int $seed;
    private int $difficulty = World::DIFFICULTY_NORMAL;
    private string $generatorOptions = "";
    private Vector3 $spawnPosition;

    public function __construct()
    {
        $this->seed = random_int(-2147483648, 2147483647);
        $this->spawnPosition = new Vector3(256, 70, 256);
    }

    public static function create(): self { return new self(); }

    /** @phpstan-return class-string<Generator> */
    public function getGeneratorClass(): string { return $this->generatorClass; }

    /** @phpstan-param class-string<Generator> $generatorClass */
    public function setGeneratorClass(string $generatorClass): self
    {
        Utils::testValidInstance($generatorClass, Generator::class);
        $this->generatorClass = $generatorClass;
        return $this;
    }

    public function getSeed(): int { return $this->seed; }
    public function setSeed(int $seed): self { $this->seed = $seed; return $this; }
    public function getDifficulty(): int { return $this->difficulty; }
    public function setDifficulty(int $difficulty): self { $this->difficulty = $difficulty; return $this; }
    public function getGeneratorOptions(): string { return $this->generatorOptions; }
    public function setGeneratorOptions(string $generatorOptions): self { $this->generatorOptions = $generatorOptions; return $this; }
    public function getSpawnPosition(): Vector3 { return $this->spawnPosition; }
    public function setSpawnPosition(Vector3 $spawnPosition): self { $this->spawnPosition = $spawnPosition; return $this; }
}
