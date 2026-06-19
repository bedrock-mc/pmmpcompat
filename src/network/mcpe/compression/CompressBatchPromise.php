<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\compression;

class CompressBatchPromise
{
    private bool $cancelled = false;
    private ?string $result = null;
    /** @var list<\Closure(self): void> */
    private array $resolveCallbacks = [];

    public function cancel(): void { $this->cancelled = true; }
    /** @return list<\Closure(self): void> */
    public function getResolveCallbacks(): array { return $this->resolveCallbacks; }
    public function getResult(): string
    {
        if ($this->result === null) {
            throw new \LogicException('Compression result has not been resolved');
        }
        return $this->result;
    }
    public function hasResult(): bool { return $this->result !== null; }
    public function isCancelled(): bool { return $this->cancelled; }
    public function onResolve(\Closure $callback): void
    {
        if ($this->result !== null) {
            $callback($this);
            return;
        }
        $this->resolveCallbacks[] = $callback;
    }
    public function resolve(string $result): void
    {
        if ($this->cancelled) {
            return;
        }
        $this->result = $result;
        foreach ($this->resolveCallbacks as $callback) {
            $callback($this);
        }
    }
}
