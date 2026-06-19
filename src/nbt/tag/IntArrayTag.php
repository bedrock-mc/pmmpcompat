<?php

declare(strict_types=1);

namespace pocketmine\nbt\tag;

class IntArrayTag extends ScalarTag
{
    /**
     * @param list<int> $value
     */
    public function __construct(array $value)
    {
        parent::__construct(array_map('intval', array_values($value)));
    }
}
