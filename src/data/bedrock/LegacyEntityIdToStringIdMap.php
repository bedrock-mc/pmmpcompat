<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock;

class LegacyEntityIdToStringIdMap extends LegacyToStringIdMap
{
    use \pocketmine\utils\SingletonTrait;

    public function __construct(mixed ...$args) { parent::__construct([1 => 'minecraft:player']); }
}
