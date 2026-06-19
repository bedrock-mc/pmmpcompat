<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\upgrade;

use pocketmine\nbt\tag\Tag;

final class BlockStateUpgradeSchemaBlockRemap
{
    /**
     * @param array<string, Tag> $oldState
     * @param string|BlockStateUpgradeSchemaFlattenInfo $newName
     * @param array<string, Tag> $newState
     * @param list<string> $copiedState
     */
    public function __construct(
        public readonly array $oldState,
        public readonly string|BlockStateUpgradeSchemaFlattenInfo $newName,
        public readonly array $newState,
        public readonly array $copiedState = []
    ) {}

    public function equals(self $other): bool
    {
        return self::tagMapEquals($this->oldState, $other->oldState)
            && self::nameEquals($this->newName, $other->newName)
            && self::tagMapEquals($this->newState, $other->newState)
            && $this->copiedState === $other->copiedState;
    }

    /** @param array<string, Tag> $a @param array<string, Tag> $b */
    private static function tagMapEquals(array $a, array $b): bool
    {
        if (array_keys($a) !== array_keys($b)) {
            return false;
        }
        foreach ($a as $key => $tag) {
            if (get_class($tag) !== get_class($b[$key]) || $tag->getValue() !== $b[$key]->getValue()) {
                return false;
            }
        }
        return true;
    }

    private static function nameEquals(string|BlockStateUpgradeSchemaFlattenInfo $a, string|BlockStateUpgradeSchemaFlattenInfo $b): bool
    {
        if (is_string($a) || is_string($b)) {
            return $a === $b;
        }
        return $a->equals($b);
    }
}
