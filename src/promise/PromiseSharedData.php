<?php

declare(strict_types=1);

namespace pocketmine\promise;

class PromiseSharedData
{
    /** @var array<int, \Closure> */
    public array $onSuccess = [];

    /** @var array<int, \Closure> */
    public array $onFailure = [];

    public ?bool $state = null;

    public mixed $result = null;
}
