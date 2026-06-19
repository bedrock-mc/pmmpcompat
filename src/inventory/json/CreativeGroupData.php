<?php

declare(strict_types=1);

namespace pocketmine\inventory\json;

class CreativeGroupData
{
    /** @param array<int, mixed> $items */
    public function __construct(
        public string $group_name = '',
        public mixed $group_icon = null,
        public array $items = []
    ) {}
}
