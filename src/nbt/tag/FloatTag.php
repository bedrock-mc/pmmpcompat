<?php

declare(strict_types=1);

namespace pocketmine\nbt\tag;

class FloatTag extends ScalarTag
{
    public function __construct(float $value)
    {
        parent::__construct($value);
    }
}
