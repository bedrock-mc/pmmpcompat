<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\compression;

class CompressBatchTask
{
    private ?string $result = null;

    public function __construct(
        private string $payload,
        private CompressBatchPromise $promise,
        private Compressor $compressor
    ) {}

    public function onRun(): void
    {
        $this->result = $this->compressor->compress($this->payload);
    }

    public function onCompletion(): void
    {
        if ($this->result === null) {
            $this->onRun();
        }
        $this->promise->resolve($this->result);
    }
}
