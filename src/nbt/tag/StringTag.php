<?php

declare(strict_types=1);

namespace pocketmine\nbt\tag;

class StringTag extends ScalarTag
{
    public function __construct(string $value)
    {
        parent::__construct($value);
    }
}
