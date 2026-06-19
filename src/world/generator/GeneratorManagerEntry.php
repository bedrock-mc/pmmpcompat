<?php

declare(strict_types=1);

namespace pocketmine\world\generator;

final class GeneratorManagerEntry
{
    public function __construct(
        private string $generatorClass,
        private \Closure $presetValidator,
        private readonly bool $fast,
    ) {}

    public function getGeneratorClass(): string { return $this->generatorClass; }
    public function isFast(): bool { return $this->fast; }

    public function validateGeneratorOptions(string $generatorOptions): void
    {
        $exception = ($this->presetValidator)($generatorOptions);
        if ($exception !== null) {
            throw $exception;
        }
    }
}
