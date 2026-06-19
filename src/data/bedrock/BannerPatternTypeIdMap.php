<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock;

class BannerPatternTypeIdMap
{
    use \pocketmine\utils\SingletonTrait;
    use CompatIdMapTrait;

    public function __construct(mixed ...$args) { $this->seedEnumCases(\pocketmine\block\utils\BannerPatternType::class); }
    public function fromId(mixed ...$args): mixed { return $this->compatFromId($args[0] ?? null); }
    public function register(mixed ...$args): mixed { return $this->compatRegister($args[0] ?? null, $args[1] ?? null); }
    public function toId(mixed ...$args): mixed { return $this->compatToId($args[0] ?? null); }
}
