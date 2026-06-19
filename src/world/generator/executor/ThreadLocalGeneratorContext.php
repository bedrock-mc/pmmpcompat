<?php

declare(strict_types=1);

namespace pocketmine\world\generator\executor;

use pocketmine\world\generator\Generator;

final class ThreadLocalGeneratorContext
{
    /** @var array<int, self> */
    private static array $contexts = [];

    public function __construct(
        private Generator $generator,
        private int $worldMinY,
        private int $worldMaxY,
    ) {}

    public static function register(self $context, int $worldId): void { self::$contexts[$worldId] = $context; }
    public static function unregister(int $worldId): void { unset(self::$contexts[$worldId]); }
    public static function fetch(int $worldId): ?self { return self::$contexts[$worldId] ?? null; }
    public function getGenerator(): Generator { return $this->generator; }
    public function getWorldMinY(): int { return $this->worldMinY; }
    public function getWorldMaxY(): int { return $this->worldMaxY; }
}
