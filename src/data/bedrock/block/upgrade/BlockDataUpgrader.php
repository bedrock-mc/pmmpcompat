<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\upgrade;

use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\nbt\tag\CompoundTag;

final class BlockDataUpgrader
{
    public function __construct(
        private BlockIdMetaUpgrader $blockIdMetaUpgrader,
        private BlockStateUpgrader $blockStateUpgrader
    ) {}

    public function upgradeIntIdMeta(int $legacyId, int $meta): BlockStateData
    {
        $state = $this->blockIdMetaUpgrader->fromIntIdMeta($legacyId, $meta);
        if ($state === null) {
            throw new \RuntimeException("Unmapped legacy block ID/meta $legacyId:$meta");
        }
        return $this->blockStateUpgrader->upgrade($state);
    }

    public function upgradeStringIdMeta(string $stringId, int $meta): BlockStateData
    {
        $state = $this->blockIdMetaUpgrader->fromStringIdMeta($stringId, $meta);
        if ($state === null) {
            throw new \RuntimeException("Unmapped legacy block ID/meta $stringId:$meta");
        }
        return $this->blockStateUpgrader->upgrade($state);
    }

    public function upgradeBlockStateNbt(CompoundTag|array $tag): BlockStateData
    {
        return $this->blockStateUpgrader->upgrade(BlockStateData::fromNbt($tag));
    }

    public function getBlockIdMetaUpgrader(): BlockIdMetaUpgrader
    {
        return $this->blockIdMetaUpgrader;
    }

    public function getBlockStateUpgrader(): BlockStateUpgrader
    {
        return $this->blockStateUpgrader;
    }
}
