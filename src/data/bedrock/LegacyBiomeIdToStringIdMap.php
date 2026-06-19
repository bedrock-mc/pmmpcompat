<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock;

class LegacyBiomeIdToStringIdMap extends LegacyToStringIdMap
{
    use \pocketmine\utils\SingletonTrait;

    public function __construct(mixed ...$args) { parent::__construct([\pocketmine\data\bedrock\BiomeIds::PLAINS => 'minecraft:plains']); }
}
