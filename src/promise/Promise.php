<?php

declare(strict_types=1);

namespace pocketmine\promise;

class Promise
{
    public function __construct(private PromiseSharedData $shared) {}

    /**
     * @param array<mixed, Promise> $promises
     */
    public static function all(array $promises): Promise
    {
        $resolver = new PromiseResolver();
        $remaining = count($promises);
        $results = [];
        $rejected = false;

        if ($remaining === 0) {
            $resolver->resolve([]);
            return $resolver->getPromise();
        }

        foreach ($promises as $key => $promise) {
            $promise->onCompletion(
                static function (mixed $result) use ($key, &$results, &$remaining, &$rejected, $resolver): void {
                    if ($rejected) {
                        return;
                    }
                    $results[$key] = $result;
                    --$remaining;
                    if ($remaining === 0) {
                        $resolver->resolve($results);
                    }
                },
                static function () use (&$rejected, $resolver): void {
                    if ($rejected) {
                        return;
                    }
                    $rejected = true;
                    $resolver->reject();
                }
            );
        }

        return $resolver->getPromise();
    }

    public function isResolved(): bool
    {
        return $this->shared->state === true;
    }

    public function onCompletion(\Closure $onSuccess, \Closure $onFailure): void
    {
        if ($this->shared->state === true) {
            $onSuccess($this->shared->result);
            return;
        }
        if ($this->shared->state === false) {
            $onFailure();
            return;
        }

        $this->shared->onSuccess[spl_object_id($onSuccess)] = $onSuccess;
        $this->shared->onFailure[spl_object_id($onFailure)] = $onFailure;
    }
}
