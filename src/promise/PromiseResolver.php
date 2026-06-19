<?php

declare(strict_types=1);

namespace pocketmine\promise;

class PromiseResolver
{
    private PromiseSharedData $shared;

    private Promise $promise;

    public function __construct()
    {
        $this->shared = new PromiseSharedData();
        $this->promise = new Promise($this->shared);
    }

    public function getPromise(): Promise
    {
        return $this->promise;
    }

    public function reject(): void
    {
        if ($this->shared->state !== null) {
            throw new \LogicException('Promise has already been resolved or rejected');
        }

        $this->shared->state = false;
        foreach ($this->shared->onFailure as $onFailure) {
            $onFailure();
        }
        $this->shared->onSuccess = [];
        $this->shared->onFailure = [];
    }

    public function resolve(mixed $value): void
    {
        if ($this->shared->state !== null) {
            throw new \LogicException('Promise has already been resolved or rejected');
        }

        $this->shared->state = true;
        $this->shared->result = $value;
        foreach ($this->shared->onSuccess as $onSuccess) {
            $onSuccess($value);
        }
        $this->shared->onSuccess = [];
        $this->shared->onFailure = [];
    }
}
