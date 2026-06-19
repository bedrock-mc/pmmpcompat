<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\item\upgrade;

use pocketmine\data\bedrock\block\upgrade\BlockDataUpgrader;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\data\bedrock\item\SavedItemStackData;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;

final class ItemDataUpgrader
{
    private const TAG_LEGACY_ID = 'id';

    public function __construct(
        private ItemIdMetaUpgrader $idMetaUpgrader,
        private LegacyItemIdToStringIdMap $legacyIntToStringIdMap,
        private R12ItemIdToBlockIdMap $r12ItemIdToBlockIdMap,
        private BlockDataUpgrader $blockDataUpgrader
    ) {}

    public function upgradeItemTypeDataString(string $rawNameId, int $meta, int $count, ?CompoundTag $nbt): SavedItemStackData
    {
        $blockStateData = null;
        if (($r12BlockId = $this->r12ItemIdToBlockIdMap->itemIdToBlockId($rawNameId)) !== null) {
            $blockStateData = $this->blockDataUpgrader->upgradeStringIdMeta($r12BlockId, $meta);
        }
        [$newNameId, $newMeta] = $this->idMetaUpgrader->upgrade($rawNameId, $meta);
        return new SavedItemStackData(new SavedItemData($newNameId, $newMeta, $blockStateData, $nbt), $count, null, null, [], []);
    }

    public function upgradeItemTypeDataInt(int $legacyNumericId, int $meta, int $count, ?CompoundTag $nbt): SavedItemStackData
    {
        $rawNameId = $this->legacyIntToStringIdMap->legacyToString($legacyNumericId);
        if ($rawNameId === null) {
            throw new \RuntimeException("Unmapped legacy item ID $legacyNumericId");
        }
        return $this->upgradeItemTypeDataString($rawNameId, $meta, $count, $nbt);
    }

    public function upgradeItemStackNbt(CompoundTag $tag): ?SavedItemStackData
    {
        $nameTag = $tag->getTag(SavedItemData::TAG_NAME);
        $legacyIdTag = $tag->getTag(self::TAG_LEGACY_ID);
        if ($nameTag instanceof StringTag) {
            $rawNameId = $nameTag->getValue();
        } elseif ($legacyIdTag instanceof ShortTag) {
            if ($legacyIdTag->getValue() === 0) {
                return null;
            }
            $rawNameId = $this->legacyIntToStringIdMap->legacyToString($legacyIdTag->getValue());
            if ($rawNameId === null) {
                throw new \RuntimeException('Legacy item ID ' . $legacyIdTag->getValue() . " doesn't map to any modern string ID");
            }
        } elseif ($legacyIdTag instanceof StringTag) {
            $rawNameId = $legacyIdTag->getValue();
        } else {
            throw new \RuntimeException('Item stack data should have either a name ID or a legacy ID');
        }

        $meta = $tag->getShort(SavedItemData::TAG_DAMAGE, 0);
        $count = $tag->getByte(SavedItemStackData::TAG_COUNT, 1);
        $slotTag = $tag->getTag(SavedItemStackData::TAG_SLOT);
        $pickedUpTag = $tag->getTag(SavedItemStackData::TAG_WAS_PICKED_UP);
        $stack = $this->upgradeItemTypeDataString($rawNameId, $meta, $count < 0 ? $count + 256 : $count, $tag->getCompoundTag(SavedItemData::TAG_TAG));
        return new SavedItemStackData(
            $stack->getTypeData(),
            $stack->getCount(),
            $slotTag instanceof ByteTag ? (($slotTag->getValue() < 0) ? $slotTag->getValue() + 256 : $slotTag->getValue()) : null,
            $pickedUpTag instanceof ByteTag ? $pickedUpTag->getValue() !== 0 : null,
            [],
            []
        );
    }

    public function getIdMetaUpgrader(): ItemIdMetaUpgrader
    {
        return $this->idMetaUpgrader;
    }
}
