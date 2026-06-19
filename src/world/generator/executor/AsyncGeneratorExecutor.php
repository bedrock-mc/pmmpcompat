<?php

declare(strict_types=1);

namespace pocketmine\world\generator\executor;

final class AsyncGeneratorExecutor implements GeneratorExecutor
{
    private SyncGeneratorExecutor $fallback;

    public function __construct(GeneratorExecutorSetupParameters $setupParameters)
    {
        $this->fallback = new SyncGeneratorExecutor($setupParameters);
    }

    public function populate(int $chunkX, int $chunkZ, mixed $centerChunk, array $adjacentChunks, \Closure $onCompletion): void
    {
        $this->fallback->populate($chunkX, $chunkZ, $centerChunk, $adjacentChunks, $onCompletion);
    }

    public function shutdown(): void { $this->fallback->shutdown(); }
}
