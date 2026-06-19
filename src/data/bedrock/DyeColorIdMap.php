<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock;

class DyeColorIdMap
{
    use \pocketmine\utils\SingletonTrait;

    public function __construct(mixed ...$args) {}
    public function fromInvertedId(mixed ...$args): mixed { return $this->fromOrdinal(15 - (int) ($args[0] ?? 0)); }
    public function fromItemId(mixed ...$args): mixed { return $this->fromOrdinal((int) ($args[0] ?? 0)); }
    public function toInvertedId(mixed ...$args): mixed { return 15 - $this->ordinal($args[0] ?? null); }
    public function toItemId(mixed ...$args): mixed { return $this->ordinal($args[0] ?? null); }
    private function fromOrdinal(int $id): mixed { $cases = \pocketmine\block\utils\DyeColor::cases(); return $cases[$id] ?? null; }
    private function ordinal(mixed $color): int { return is_object($color) ? max(0, array_search($color, \pocketmine\block\utils\DyeColor::cases(), true) ?: 0) : 0; }}
