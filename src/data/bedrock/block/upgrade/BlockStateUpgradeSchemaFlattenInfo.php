<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\upgrade;

use pocketmine\nbt\tag\Tag;

final class BlockStateUpgradeSchemaFlattenInfo
{
    /**
     * @param class-string<Tag>|null $flattenedPropertyType
     * @param array<string, string> $flattenedValueRemaps
     */
    public function __construct(
        public readonly string $prefix,
        public readonly string $flattenedProperty,
        public readonly ?string $flattenedPropertyType,
        public readonly string $suffix,
        public readonly array $flattenedValueRemaps = []
    ) {}

    public function equals(self $other): bool
    {
        return $this->prefix === $other->prefix
            && $this->flattenedProperty === $other->flattenedProperty
            && $this->flattenedPropertyType === $other->flattenedPropertyType
            && $this->suffix === $other->suffix
            && $this->flattenedValueRemaps === $other->flattenedValueRemaps;
    }
}
