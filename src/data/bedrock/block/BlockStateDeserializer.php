<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block;

/**
 * Implementors decide how Bedrock block state data is represented in runtime paletted storage.
 */
interface BlockStateDeserializer
{
    public function deserialize(BlockStateData $stateData): int;
}
