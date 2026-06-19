<?php

declare(strict_types=1);

namespace pocketmine\nbt\tag;

abstract class Tag
{
    abstract public function getValue(): mixed;

    /**
     * @return array{type: string, value: mixed}
     */
    public function toCompatibilityData(): array
    {
        return ['type' => static::class, 'value' => $this->getValue()];
    }
}
