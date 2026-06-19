<?php

declare(strict_types=1);

namespace pocketmine\player;

use pocketmine\lang\Translatable;

final class GameMode implements \JsonSerializable
{
    private const SURVIVAL = 'survival';
    private const CREATIVE = 'creative';
    private const ADVENTURE = 'adventure';
    private const SPECTATOR = 'spectator';

    /** @param string[] $aliases */
    private function __construct(private string $id, private string $englishName, private array $aliases) {}

    public static function SURVIVAL(): self { return new self(self::SURVIVAL, 'Survival', ['survival', 's', '0']); }
    public static function CREATIVE(): self { return new self(self::CREATIVE, 'Creative', ['creative', 'c', '1']); }
    public static function ADVENTURE(): self { return new self(self::ADVENTURE, 'Adventure', ['adventure', 'a', '2']); }
    public static function SPECTATOR(): self { return new self(self::SPECTATOR, 'Spectator', ['spectator', 'v', 'view', '3']); }

    public static function fromString(string $id): self
    {
        return match (strtolower($id)) {
            self::CREATIVE, '1', 'c' => self::CREATIVE(),
            self::ADVENTURE, '2', 'a' => self::ADVENTURE(),
            self::SPECTATOR, '3', 'v', 'view', 'spc' => self::SPECTATOR(),
            default => self::SURVIVAL(),
        };
    }

    public function getEnglishName(): string { return $this->englishName; }
    public function getTranslatableName(): Translatable { return new Translatable('gameMode.' . $this->id); }
    /** @return string[] */
    public function getAliases(): array { return $this->aliases; }
    public function getId(): string { return $this->id; }
    public function equals(self $other): bool { return $this->id === $other->id; }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id, 'name' => $this->englishName];
    }
}
