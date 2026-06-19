<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block;

/**
 * Implementors decide how runtime blockstate IDs are represented as Bedrock block state data.
 */
interface BlockStateSerializer
{
    public function serialize(int $stateId): BlockStateData;
}
