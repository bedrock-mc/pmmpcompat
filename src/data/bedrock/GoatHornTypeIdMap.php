<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock;

class GoatHornTypeIdMap
{
    use \pocketmine\utils\SingletonTrait;
    use CompatIdMapTrait;

    public function __construct(mixed ...$args) { $this->seedEnumCases(\pocketmine\item\GoatHornType::class); }
}
