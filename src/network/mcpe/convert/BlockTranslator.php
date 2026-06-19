<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

use pocketmine\data\bedrock\block\BlockStateData;

final class BlockTranslator
{
    public function __construct(private ?BlockStateDictionary $blockStateDictionary = null)
    {
        $this->blockStateDictionary ??= new BlockStateDictionary();
    }

    public function getBlockStateDictionary(): BlockStateDictionary { return $this->blockStateDictionary; }
    public function getFallbackStateData(): BlockStateData { return new BlockStateData('minecraft:air'); }
    public function internalIdToNetworkId(string|int $stateId): int { return (int) ($this->blockStateDictionary->lookupStateIdFromIdMeta((string) $stateId) ?? crc32((string) $stateId)); }
    public function internalIdToNetworkStateData(string|int $stateId): BlockStateData { return new BlockStateData((string) $stateId); }
}
