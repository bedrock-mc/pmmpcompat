<?php

declare(strict_types=1);

namespace pocketmine\nbt\tag;

class LongTag extends ScalarTag
{
    public function __construct(int $value)
    {
        parent::__construct($value);
    }
}
