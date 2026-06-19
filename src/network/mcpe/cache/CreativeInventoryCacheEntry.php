<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\cache;

class CreativeInventoryCacheEntry
{
    /**
     * @param array<int, mixed> $categories
     * @param array<int, mixed> $groups
     * @param array<int, mixed> $items
     */
    public function __construct(
        public readonly array $categories = [],
        public readonly array $groups = [],
        public readonly array $items = []
    ) {}
}
