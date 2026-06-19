<?php

declare(strict_types=1);

namespace pocketmine\world\generator\executor;

use pocketmine\world\generator\Generator;

final class GeneratorExecutorSetupParameters
{
    public function __construct(
        public readonly int $worldMinY,
        public readonly int $worldMaxY,
        public readonly int $generatorSeed,
        public readonly string $generatorClass,
        public readonly string $generatorSettings,
    ) {}

    public function createGenerator(): Generator
    {
        $generator = new $this->generatorClass($this->generatorSeed, $this->generatorSettings);
        if (!$generator instanceof Generator) {
            throw new \UnexpectedValueException($this->generatorClass . ' did not create a Generator');
        }
        return $generator;
    }
}
