<?php

declare(strict_types=1);

namespace pocketmine\thread;

class NonThreadSafeValue
{
    private string $variable;

    public function __construct(mixed $variable)
    {
        $serialized = serialize($variable);
        if ($serialized === false) {
            throw new \InvalidArgumentException('Cannot serialize value');
        }
        $this->variable = $serialized;
    }

    public function deserialize(): mixed
    {
        return unserialize($this->variable);
    }
}
