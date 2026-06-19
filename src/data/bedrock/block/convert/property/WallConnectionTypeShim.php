<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert\property;

use pocketmine\block\utils\WallConnectionType;

enum WallConnectionTypeShim
{
    case NONE;
    case SHORT;
    case TALL;

    public static function fromRaw(string $raw): self
    {
        return match ($raw) {
            'short' => self::SHORT,
            'tall' => self::TALL,
            default => self::NONE,
        };
    }

    public function deserialize(): ?WallConnectionType
    {
        return match ($this) {
            self::NONE => null,
            self::SHORT => WallConnectionType::SHORT,
            self::TALL => WallConnectionType::TALL,
        };
    }
    public function getValue(): string
    {
        return match ($this) {
            self::NONE => 'none',
            self::SHORT => 'short',
            self::TALL => 'tall',
        };
    }
    public static function serialize(?WallConnectionType $value): self
    {
        return match ($value) {
            null => self::NONE,
            WallConnectionType::SHORT => self::SHORT,
            WallConnectionType::TALL => self::TALL,
        };
    }
}
