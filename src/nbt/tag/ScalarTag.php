<?php

declare(strict_types=1);

namespace pocketmine\nbt\tag;

abstract class ScalarTag extends Tag
{
    public function __construct(protected mixed $value)
    {
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }
}
