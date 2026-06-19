<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

class ChunkRequestTask
{
    private string $payload = '';

    public function __construct(
        private mixed $world,
        private int $chunkX,
        private int $chunkZ,
        private mixed $onCompletion = null,
    ) {}

    public function onRun(): void
    {
        $this->payload = json_encode(['chunkX' => $this->chunkX, 'chunkZ' => $this->chunkZ], JSON_THROW_ON_ERROR);
    }

    public function onCompletion(): void
    {
        if ($this->onCompletion instanceof \Closure) {
            ($this->onCompletion)($this->chunkX, $this->chunkZ, $this->payload);
        }
    }

    public function getPayload(): string { return $this->payload; }
}
