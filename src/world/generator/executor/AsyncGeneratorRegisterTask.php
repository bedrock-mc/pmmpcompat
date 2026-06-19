<?php

declare(strict_types=1);

namespace pocketmine\world\generator\executor;

final class AsyncGeneratorRegisterTask
{
    public function __construct(
        private int $worldId,
        private GeneratorExecutorSetupParameters $setupParameters,
    ) {}

    public function onRun(): void
    {
        ThreadLocalGeneratorContext::register(new ThreadLocalGeneratorContext(
            $this->setupParameters->createGenerator(),
            $this->setupParameters->worldMinY,
            $this->setupParameters->worldMaxY,
        ), $this->worldId);
    }
}
